<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\CompanySubscriptionType;
use App\Application\Form\Type\EditCompanySubscriptionType;
use App\Application\Form\Type\PaymentMethodType;
use App\Application\Service\CompanySubscriptionService;
use App\Application\Service\CompanyService;
use App\Application\Service\PaymentMethodService;
use App\Application\Service\SubscriptionService;
use App\Application\Service\SubscriptionStatusService;
use App\Domain\Model\Company;
use App\Domain\Model\CompanySubscription;
use App\Domain\Model\PaymentMethod;
use App\Infrastructure\Factory\NotificationFactory;
use App\Infrastructure\Repository\SubscriptionStatusRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Stripe\StripeClient;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

final class CompanySubscriptionController extends AbstractFOSRestController
{
    private $entityManager;
    private $companySubscriptionService;
    private $companyService;
    private $translator;
    private $notificationFactory;
    private $paymentMethodService;
    private $subscriptionStatusService;
    private $subscriptionService;


    /**
     * CompanySubscriptionRestController constructor.
     */
    public function __construct(
        CompanySubscriptionService $companySubscriptionService,
        CompanyService $companyService,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        NotificationFactory $notificationFactory,
        PaymentMethodService $paymentMethodService,
        SubscriptionStatusService $subscriptionStatusService,
        SubscriptionService $subscriptionService

    ) {
        $this->companySubscriptionService = $companySubscriptionService;
        $this->companyService = $companyService;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->notificationFactory = $notificationFactory;
        $this->paymentMethodService = $paymentMethodService;
        $this->subscriptionStatusService = $subscriptionStatusService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @Rest\View(serializerGroups={"api_company_subscriptions"})
     * @Rest\Get("admin/company-subscription/{companySubscriptionId}")
     *
     * @return View
     */
    public function getCompanySubscription(int $companySubscriptionId): View
    {
        $companySubscription = $this->companySubscriptionService->getCompanySubscription($companySubscriptionId);

        return View::create($companySubscription, Response::HTTP_OK);
    }

    /**
     * @Rest\View(serializerGroups={"api_company_subscriptions"})
     * @Rest\Post("admin/company-subscription/{companySubscriptionId}")
     *
     * @return View
     */
    public function editCompanySubscription(int $companySubscriptionId, Request $request)
    {
        $companySubscription = $this->companySubscriptionService->getCompanySubscription($companySubscriptionId);
        if (!$this->isCsrfTokenValid('companySubscription', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$companySubscription) {
            throw new EntityNotFoundException('CompanySubscription with id ' . $companySubscriptionId . ' does not exist.');
        }
        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(EditCompanySubscriptionType::class, $companySubscription, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }

        $this->entityManager->persist($companySubscription);
        $this->entityManager->flush($companySubscription);

        $ressourceLocation = $this->generateUrl('company_validated_show', ['id' => $companySubscription->getCompany()->getId()]);

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Post("/subscribes")
     * @param Request $request
     *
     * @return View
     */
    public function createCompanySubscription(Request $request)
    {
        if (!$this->isCsrfTokenValid('companySubscription', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $isTrial = $request->request->get('isTrial');
        $request->request->remove('isTrial');
        $companyId = $request->request->get('company');

        $activeCompanySubscription = $this->companySubscriptionService->getActiveSubscription($companyId, false, true);
        if (empty($activeCompanySubscription)) {
            $activeCompanySubscription = $this->companySubscriptionService->getActiveSubscription($companyId, false, false);
        }
        if (!empty($activeCompanySubscription) && $activeCompanySubscription->getSubscriptionStatus()->getCode() != SubscriptionStatusRepository::OFFER_CODE) {
            $subscriptionStatus = $this->subscriptionStatusService->getSubscriptionStatusByCode(SubscriptionStatusRepository::PENDING_CODE);
            $activeCompanySubscription->setSubscriptionStatus($subscriptionStatus);
            $activeCompanySubscription->setStripeId($request->request->get('stripeId'));
            $this->entityManager->persist($activeCompanySubscription);
            $this->entityManager->flush();
            if ($isTrial == false) {
                $user = $activeCompanySubscription->getCompany()->getUtilisateur();
                $ressourceLocation = $this->generateUrl('subscription_details_subscriptions', ['slug' => strtolower($activeCompanySubscription->getSubscription()->getName())]);
                $this->notificationFactory->createCompanySubscription([$user], $ressourceLocation, $activeCompanySubscription);
            }
        } else {
            $companySubscription = new CompanySubscription();
            $subscriptionStatus = $this->subscriptionStatusService->getSubscriptionStatusByCode(SubscriptionStatusRepository::PENDING_CODE);
            $request->request->set('subscriptionStatus', $subscriptionStatus->getId());
            $formOptions = ['translator' => $this->translator];
            $form = $this->createForm(CompanySubscriptionType::class, $companySubscription, $formOptions);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return $form;
            }
            $this->entityManager->persist($companySubscription);
            $this->entityManager->flush();

            $email = $companySubscription->getCompany()->getEmail();
            $subject = $this->translator->trans('email_pending_payment_subject');
            $bodyMessage = sprintf(
                $this->translator->trans('email_pending_payment_message'),
                $companySubscription->getCompany()->getName(),
                $companySubscription->getSubscription()->getName(),
                $companySubscription->getSubscription()->getName()
            );
            $this->sendMail($email, $subject, $bodyMessage);
            if ($isTrial == false) {
                $user = $companySubscription->getCompany()->getUtilisateur();
                $ressourceLocation = $this->generateUrl('subscription_details_subscriptions', ['slug' => strtolower($companySubscription->getSubscription()->getName())]);
                $this->notificationFactory->createCompanySubscription([$user], $ressourceLocation, $companySubscription);
            }
        }
        $ressourceLocation = $this->generateUrl('dashboard');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }


    /**
     * @Rest\View(serializerGroups={"api_company_subscriptions"})
     * @Rest\Get("setup-intent/{companyId}")
     *
     * @return View
     */
    public function getStripeSetupIntentByCompany(int $companyId): View
    {
        $company = $this->companyService->getCompany($companyId);
        $stripe = new StripeClient($this->getParameter('secret_key'));
        $customer = $this->getStripeCustomer($company, $stripe);
        try {
            $setupIntent = $stripe->setupIntents->create([
                'customer' => $customer['id'],
                'payment_method_types' => ['sepa_debit']
            ]);
        } catch (Exception $e) {
            return View::create($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return View::create($setupIntent->client_secret, Response::HTTP_OK);
    }

    /**
     * @Rest\View(serializerGroups={"api_company_subscriptions"})
     * @Rest\Get("intent/{companyId}")
     * @QueryParam(name="price_id",
     *             default="",
     *             description="stripe id du prix de l'abonnement"
     * )
     *
     * @return View
     */
    public function getStripeIntentByCompany(int $companyId, ParamFetcher $paramFetcher): View
    {
        $company = $this->companyService->getCompany($companyId);
        $stripe = new StripeClient($this->getParameter('secret_key'));
        $customer = $this->getStripeCustomer($company, $stripe);
        $stripePriceId = $paramFetcher->get('price_id');
        $price = $this->subscriptionService->getSubscriptionByStripeId($stripePriceId)->getPrice();
        try {
            $intent = $stripe->paymentIntents->create([
                'amount' => $price * 100,
                'currency' => 'eur',
                'payment_method_types' => ['card'],
                'customer' => $customer['id'],
                'setup_future_usage' => 'off_session'
            ]);
        } catch (Exception $e) {
            return View::create($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return View::create($intent->client_secret, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("/default-payment")
     * @param Request $request
     *
     * @return View
     */
    public function setPaymentMethodAndPay(Request $request)
    {
        if (!$this->isCsrfTokenValid('companySubscription', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $companyId = $request->request->get('company');
        $stripeId = $request->request->get('stripeId');
        $subscriptionId = $request->request->get('subscription');
        $request->request->remove('subscription');
        $dtStart = $request->request->get('dtStart');
        $request->request->remove('dtStart');
        $type = $request->request->get('type');
        $request->request->remove('type');
        $subscription = $this->subscriptionService->getSubscription($subscriptionId);
        $priceStripeId = $subscription->getStripeId();
        $company = $this->companyService->getCompany($companyId);
        $stripe = new StripeClient($this->getParameter('secret_key'));
        $customer = $this->getStripeCustomer($company, $stripe);
        try {
            $stripe->customers->update(
                $customer['id'],
                [
                    'invoice_settings' => [
                        'default_payment_method' => $stripeId
                    ]
                ]
            );
            // SetPaymentMethod For company
            $paymentMethodRetrieved = $stripe->paymentMethods->retrieve($stripeId);
            $paymentMethod = new PaymentMethod();
            if ($type === 'iban') {
                $request->request->set('last4', $paymentMethodRetrieved['sepa_debit']['last4']);
                $request->request->set('fingerprint', $paymentMethodRetrieved['sepa_debit']['fingerprint']);
                $request->request->set('country', $paymentMethodRetrieved['sepa_debit']['country']);
            } else if ($type === 'card') {
                $request->request->set('last4', $paymentMethodRetrieved['card']['last4']);
                $request->request->set('fingerprint', $paymentMethodRetrieved['card']['fingerprint']);
                $request->request->set('country', $paymentMethodRetrieved['card']['country']);
            }

            $paymentMethod->setDefaultMethod(true);
            $formOptions = ['translator' => $this->translator];
            $form = $this->createForm(PaymentMethodType::class, $paymentMethod, $formOptions);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return $form;
            }

            $this->entityManager->persist($paymentMethod);
            $this->entityManager->flush();
            $subscription = $this->createStripeSubscription($stripe, $customer, $priceStripeId, $dtStart);
        } catch (Exception $e) {
            return View::create($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return View::create($subscription, Response::HTTP_CREATED);
    }

    /**
     * @Rest\View()
     * @Rest\Post("/payment")
     * @param Request $request
     *
     * @return View
     */
    public function subscribeWithDefaultPaymentMethod(Request $request)
    {
        if (!$this->isCsrfTokenValid('companySubscription', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $companyId = $request->request->get('company');
        $requestDtStart = $request->request->get('dtStart');
        $subscriptionId = $request->request->get('subscription');
        $subscription = $this->subscriptionService->getSubscription($subscriptionId);
        if ($requestDtStart) {
            $dtStart = new DateTime();
            $dtStart->setTimestamp($requestDtStart);
            $dtStart = $dtStart->getTimestamp();
        }


        //Récupérer l'id du prix depuis la table subscription. Gérer l'initialisation de cet id dans la bdd.
        $priceStripeId = $subscription->getStripeId();
        $company = $this->companyService->getCompany($companyId);
        $stripe = new StripeClient($this->getParameter('secret_key'));
        $customer = $this->getStripeCustomer($company, $stripe);

        try {
            if (!empty($dtStart)) {
                $subscription = $stripe->subscriptions->create([
                    'customer' => $customer['id'],
                    'items' => [[
                        'price' => $priceStripeId,
                    ]],
                    'expand' => ['latest_invoice.payment_intent'],
                    'trial_end' => $dtStart
                ]);
            } else {
                $subscription = $stripe->subscriptions->create([
                    'customer' => $customer['id'],
                    'items' => [[
                        'price' => $priceStripeId,
                    ]],
                    'expand' => ['latest_invoice.payment_intent']
                ]);
            }
        } catch (Exception $e) {
            return View::create($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return View::create($subscription, Response::HTTP_CREATED);
    }

    /**
     * @Rest\View()
     * @Rest\Post("/cancel-subscription")
     * @param Request $request
     *
     * @return View
     */
    public function cancelSubscription(Request $request)
    {
        if (!$this->isCsrfTokenValid('companySubscription', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $companyId = $request->request->get('company');
        $activeSubscription = $this->companySubscriptionService->getActiveSubscription($companyId, true, false);
        $subscriptionStripeId = $activeSubscription->getStripeId();
        $stripe = new StripeClient($this->getParameter('secret_key'));
        $canceledSubscriptionStatus = $this->subscriptionStatusService->getSubscriptionStatusByCode('ANN');

        try {
            $subscription = $stripe->subscriptions->retrieve($subscriptionStripeId);
            $subscription->cancel();
            $activeSubscription->setStripeId(null);
            $activeSubscription->setSubscriptionStatus($canceledSubscriptionStatus);

            $this->entityManager->persist($activeSubscription);
            $this->entityManager->flush();
        } catch (Exception $e) {
            return View::create($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $ressourceLocation = $this->generateUrl('dashboard',);
        //Send notification to user
        $user = $activeSubscription->getCompany()->getUtilisateur();
        $ressourceLocation = $this->generateUrl('subscription_details_subscriptions', ['slug' => strtolower($activeSubscription->getSubscription()->getName())]);
        $this->notificationFactory->cancelCompanySubscription([$user], $ressourceLocation, $activeSubscription);
        $ressourceLocation = $this->generateUrl('dashboard');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Post("/update-payment-method")
     * @param Request $request
     *
     * @return View
     */
    public function updateDefaultPayment(Request $request)
    {
        if (!$this->isCsrfTokenValid('companySubscription', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $companyId = $request->request->get('company');
        $paymentMethodId = $request->request->get('stripeId');
        $subscriptionName = $request->request->get('subscriptionName');
        $request->request->remove('subscriptionName');
        $type = $request->request->get('type');
        $request->request->remove('type');
        $company = $this->companyService->getCompany($companyId);
        $stripe = new StripeClient($this->getParameter('secret_key'));
        $customer = $this->getStripeCustomer($company, $stripe);
        try {
            $stripe->paymentMethods->attach(
                $paymentMethodId,
                ['customer' => $customer['id']]
            );
            $stripe->customers->update(
                $customer['id'],
                [
                    'invoice_settings' => ['default_payment_method' => $paymentMethodId]
                ]
            );
            //Unset default older paymentMethod
            $oldPaymentMethod = $this->paymentMethodService->getDefaultPaymentMethod($companyId);
            $oldPaymentMethod->setDefaultMethod(false);
            $this->entityManager->persist($oldPaymentMethod);

            // SetPaymentMethod For company
            $paymentMethodRetrieved = $stripe->paymentMethods->retrieve($paymentMethodId);
            dump($paymentMethodRetrieved);
            $paymentMethod = new PaymentMethod();
            if ($type === 'iban') {
                $request->request->set('last4', $paymentMethodRetrieved['sepa_debit']['last4']);
                $request->request->set('fingerprint', $paymentMethodRetrieved['sepa_debit']['fingerprint']);
                $request->request->set('country', $paymentMethodRetrieved['sepa_debit']['country']);
            } else if ($type === 'card') {
                $request->request->set('last4', $paymentMethodRetrieved['card']['last4']);
                $request->request->set('fingerprint', $paymentMethodRetrieved['card']['fingerprint']);
                $request->request->set('country', $paymentMethodRetrieved['card']['country']);
            }
            $paymentMethod->setDefaultMethod(true);
            $formOptions = ['translator' => $this->translator];
            $form = $this->createForm(PaymentMethodType::class, $paymentMethod, $formOptions);
            $form->submit($request->request->all());
            if (!$form->isValid()) {
                return $form;
            }

            $this->entityManager->persist($paymentMethod);
            $this->entityManager->flush();
        } catch (Exception $e) {
            return View::create($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $ressourceLocation = $this->generateUrl('subscription_details_subscriptions', ['slug' => $subscriptionName]);
        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    private function getStripeCustomer(Company $company, $stripe)
    {
        $companyStripeId = $company->getStripeId();
        $customer = null;
        if (empty($companyStripeId)) {
            $customer = $stripe->customers->create([
                'email' => $company->getEmail(),
                'name' => $company->getName(),
                'preferred_locales' => ['fr-FR']
            ]);
            $company->setStripeId($customer['id']);

            $this->entityManager->persist($company);
            $this->entityManager->flush();
        } else {
            $customer = $stripe->customers->retrieve($companyStripeId);
        }
        return $customer;
    }

    private function sendMail($receiver, $subject, $bodyMessage)
    {
        $dsn = $this->getParameter('url');
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from('no-reply@sakabay.com')
            ->to($receiver)
            ->addTo('andreasadelson@gmail.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text($bodyMessage)
            // ->html('<p>See Twig integration for better HTML integration!</p>')
        ;

        $mailer->send($email);
    }

    private function createStripeSubscription($stripe, $customer, $priceStripeId, $dtStart)
    {
        if (!empty($dtStart)) {
            $dtStartUnix = new DateTime();
            $dtStartUnix->setTimestamp($dtStart);
            $dtStartUnix = $dtStartUnix->getTimestamp();
            $subscription = $stripe->subscriptions->create([
                'customer' => $customer['id'],
                'items' => [[
                    'price' => $priceStripeId,
                ]],
                'expand' => ['latest_invoice.payment_intent'],
                'trial_end' => $dtStartUnix
            ]);
        } else {
            $subscription = $stripe->subscriptions->create([
                'customer' => $customer['id'],
                'items' => [[
                    'price' => $priceStripeId,
                ]],
                'expand' => ['latest_invoice.payment_intent']
            ]);
        }
        return $subscription;
    }
}
