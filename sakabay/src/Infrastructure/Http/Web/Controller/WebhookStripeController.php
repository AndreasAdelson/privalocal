<?php

namespace App\Infrastructure\Http\Web\Controller;

use App\Application\Service\CompanySubscriptionService;
use App\Application\Service\PaymentMethodService;
use App\Application\Service\SubscriptionStatusService;
use App\Infrastructure\Factory\NotificationFactory;
use App\Infrastructure\Repository\SubscriptionStatusRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class WebhookStripeController extends AbstractController
{

    private $entityManager;
    private $companySubscriptionService;
    private $paymentMethodService;
    private $translator;
    private $notificationFactory;
    private $subscriptionStatusService;

    /**
     * CompanySubscriptionRestController constructor.
     */
    public function __construct(
        CompanySubscriptionService $companySubscriptionService,
        PaymentMethodService $paymentMethodService,
        TranslatorInterface $translator,
        NotificationFactory $notificationFactory,
        EntityManagerInterface $entityManager,
        SubscriptionStatusService $subscriptionStatusService
    ) {
        $this->companySubscriptionService = $companySubscriptionService;
        $this->paymentMethodService = $paymentMethodService;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->notificationFactory = $notificationFactory;
        $this->subscriptionStatusService = $subscriptionStatusService;
    }

    /**
     * @Route("webhooks/stripe", name="webhook_stripe", methods="GET|POST")
     * @param Request $request
     *
     * @return View
     */
    public function stripeWebhookAction(Request $request)
    {
        \Stripe\Stripe::setApiKey($this->getParameter('secret_key'));
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new \Exception('Bad JSON body from Stripe!');
        }
        $eventId = $data['id'];

        $stripeEvent = $this->findEvent($eventId);
        switch ($stripeEvent->type) {
            case 'invoice.payment_succeeded':
                $stripeSubscriptionId = $stripeEvent->data->object->subscription;
                $amountDue = $stripeEvent->data->object->amount_due;
                if ($stripeSubscriptionId) {
                    $companySubscription = $this->companySubscriptionService->findCompanySubscriptionByStripeId($stripeSubscriptionId);
                    //Retrieve stripe subscription
                    $stripeSubscription = $this->findSubscription($stripeSubscriptionId);
                    $newPeriodEnd = \DateTime::createFromFormat('U', $stripeSubscription->current_period_end);
                    $user = $companySubscription->getCompany()->getUtilisateur();
                    $ressourceLocation = $this->generateUrl('subscription_details_subscriptions', ['slug' => strtolower($companySubscription->getSubscription()->getName())]);
                    $validateStatus = $this->subscriptionStatusService->getSubscriptionStatusByCode(SubscriptionStatusRepository::VALIDATE_CODE);
                    $this->companySubscriptionService->setStatusCompanySubscriptionByStripeId($stripeSubscriptionId, $validateStatus);
                    if ($amountDue != 0) {
                        if ($newPeriodEnd->format('Y-m-d H:i:s') > $companySubscription->getDtFin()) {
                            //Send new end date
                            $companySubscription->setDtFin($newPeriodEnd);
                            $this->entityManager->persist($companySubscription);
                            $this->entityManager->flush();
                            //email for renewal
                            $companyEmail = $companySubscription->getCompany()->getEmail();
                            $subject = $this->translator->trans('email_renewal_subscription_subject');
                            $bodyMessage = sprintf(
                                $this->translator->trans('email_renewal_subscription_body'),
                                $companySubscription->getDtFin()->format('d/m/Y \à H:i'),
                            );
                            $this->sendMail($companyEmail, $subject, $bodyMessage);
                        } else {
                            //email for new subscription
                            $companyEmail = $companySubscription->getCompany()->getEmail();
                            $subject = $this->translator->trans('email_first_subscription_subject');
                            $bodyMessage = sprintf(
                                $this->translator->trans('email_first_subscription_body'),
                                $companySubscription->getDtFin()->format('d/m/Y \à H:i')
                            );
                            $this->sendMail($companyEmail, $subject, $bodyMessage);
                        }

                        $this->notificationFactory->confirmPaymentCompanySubscription([$user], $ressourceLocation);
                    } else {
                        //notification for trial period
                        $this->notificationFactory->trialPeriodCompanySubscription([$user], $ressourceLocation, $companySubscription);
                    }
                }
                break;
            case 'invoice.payment_failed':
                $stripeSubscriptionId = $stripeEvent->data->object->subscription;
                $stripeCustomer = $this->findCustomer($stripeEvent->data->object->customer);
                $stripeDefaultPaymentMethodId = $stripeCustomer->invoice_settings->default_payment_method;
                if ($stripeSubscriptionId && $stripeDefaultPaymentMethodId) {
                    $companySubscription = $this->companySubscriptionService->findCompanySubscriptionByStripeId($stripeSubscriptionId);
                    $companyEmail = $companySubscription->getCompany()->getEmail();

                    //WARNING Be sure of the order in which you send emails for production
                    //Send email to the company explaining that the payment_failed only at first and last attempt. Its depends on the settings in stripe dashboard.
                    if ($stripeEvent->data->object->attempt_count == 1) {

                        //Subscription pending status.
                        $pendingStatus = $this->subscriptionStatusService->getSubscriptionStatusByCode(SubscriptionStatusRepository::PENDING_CODE);
                        $endStatus = $this->subscriptionStatusService->getSubscriptionStatusByCode(SubscriptionStatusRepository::END_CODE);
                        $this->companySubscriptionService->setStatusCompanySubscriptionByStripeId($stripeSubscriptionId, $endStatus);
                        $user = $companySubscription->getCompany()->getUtilisateur();

                        //Stop the subscription for the company.
                        $newEndDate = new DateTime();
                        $this->companySubscriptionService->endCompanySubscriptionByStripeId($stripeSubscriptionId, $newEndDate);
                        $this->paymentMethodService->unsetDefaultPaymentMethodByStripeId($stripeDefaultPaymentMethodId);
                        // Send notification to the user.
                        $ressourceLocation = $this->generateUrl('subscription_details_subscriptions', ['slug' => strtolower($companySubscription->getSubscription()->getName())]);
                        $this->notificationFactory->unpaidCompanySubscription([$user], $ressourceLocation);

                        //Inform user that the payment failed one time by email
                        $subject = $this->translator->trans('email_first_payment_failed_subject');
                        $bodyMessage = $this->translator->trans('email_first_payment_failed_body');
                        $this->sendMail($companyEmail, $subject, $bodyMessage);
                    } else if ($stripeEvent->data->object->attempt_count == 4) {

                        //Subscription ended status.
                        $endStatus = $this->subscriptionStatusService->getSubscriptionStatusByCode(SubscriptionStatusRepository::END_CODE);
                        $this->companySubscriptionService->setStatusCompanySubscriptionByStripeId($stripeSubscriptionId, $endStatus);

                        //Stop the subscription for the company.
                        $newEndDate = new DateTime();
                        $this->companySubscriptionService->endCompanySubscriptionByStripeId($stripeSubscriptionId, $newEndDate);
                        $this->paymentMethodService->unsetDefaultPaymentMethodByStripeId($stripeDefaultPaymentMethodId);

                        //Inform user that the invoice is unpaid and the subscription cancelled by email .
                        $subject = $this->translator->trans('email_payment_failed_subject');
                        $bodyMessage = $this->translator->trans('email_payment_failed_body');
                        $this->sendMail($companyEmail, $subject, $bodyMessage);

                        //Send notification to the user.
                        $user = $companySubscription->getCompany()->getUtilisateur();
                        $ressourceLocation = $this->generateUrl('subscription_details_subscriptions', ['slug' => strtolower($companySubscription->getSubscription()->getName())]);
                        $this->notificationFactory->unpaidCompanySubscription([$user], $ressourceLocation);
                    }
                }
                break;
            default:
                throw new \Exception('Unexpected webhook type form Stripe! ' . $stripeEvent->type);
        }
        return new Response(Response::HTTP_OK);
    }

    // A voir si on fait pas une classe Stripe
    /**
     * @param $eventId
     * @return \Stripe\Event
     */
    public function findEvent($eventId)
    {
        return \Stripe\Event::retrieve($eventId);
    }

    /**
     * @param $stripeSubscriptionId
     * @return \Stripe\Subscription
     */
    public function findSubscription($stripeSubscriptionId)
    {
        return \Stripe\Subscription::retrieve($stripeSubscriptionId);
    }

    /**
     * @param $stripeSubscriptionId
     * @return \Stripe\PaymentMethod
     */
    public function findCustomer($stripeCustomerId)
    {
        return \Stripe\Customer::retrieve($stripeCustomerId);
    }





    //Méthode denvoie de mail
    public function sendMail($receiver, $subject, $bodyMessage)
    {
        $dsn = $this->getParameter('url');
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from('no-reply@sakabay.com')
            ->to($receiver)
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->addTo('andreasadelson@gmail.com')
            ->text($bodyMessage)
            // ->html('<p>See Twig integration for better HTML integration!</p>')
        ;

        $mailer->send($email);
    }
}
