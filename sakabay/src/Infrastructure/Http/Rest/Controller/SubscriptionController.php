<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\SubscriptionType;
use App\Application\Service\SubscriptionService;
use App\Domain\Model\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Stripe\StripeClient;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SubscriptionController extends AbstractFOSRestController
{
    private $subscriptionService;
    private $translator;
    private $entityManager;

    /**
     * SubscriptionRestController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, SubscriptionService $subscriptionService, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->subscriptionService = $subscriptionService;
        $this->translator = $translator;
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/subscriptions")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     *
     * @return View
     */
    public function createSubscriptionAdmin(Request $request)
    {
        $subscription = new Subscription();
        if (!$this->isCsrfTokenValid('subscription', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        //Integrate stripe id here
        if (empty($request->request->get('stripeId'))) {
            $stripe = new StripeClient($this->getParameter('secret_key'));
            $stripePrice = $this->getStripePrice($subscription, $stripe);
            $request->request->set('stripeId', $stripePrice['id']);
        }
        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(SubscriptionType::class, $subscription, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('subscription_admin_index');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_subscriptions"})
     * @Rest\Get("/admin/subscriptions")
     *
     * @QueryParam(name="filterFields",
     *             default="name",
     *             description="Liste des champs sur lesquels le filtre s'appuie"
     * )
     * @QueryParam(name="filter",
     *             default="",
     *             description="Filtre"
     * )
     * @QueryParam(name="sortBy",
     *             default="name",
     *             description="Champ unique sur lequel s'opère le tri"
     * )
     * @QueryParam(name="sortDesc",
     *             default="false",
     *             description="Sens du tri"
     * )
     * @QueryParam(name="currentPage",
     *             default="1",
     *             description="Page courante"
     * )
     * @QueryParam(name="perPage",
     *             default="1000000",
     *             description="Taille de la page"
     * )
     * @return View
     */

    public function getSubscriptionsAdmin(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');

        $pager = $this->subscriptionService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
        $subscriptions = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($subscriptions);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }
    /**
     * @Rest\View(serializerGroups={"api_subscriptions"})
     * @Rest\Get("admin/subscriptions/{subscriptionId}")
     *
     * @return View
     */
    public function getSubscriptionAdmin(int $subscriptionId): View
    {
        $subscription = $this->subscriptionService->getSubscription($subscriptionId);
        if (!$subscription) {
            throw new EntityNotFoundException('Subscription with id ' . $subscriptionId . ' does not exist!');
        }
        return View::create($subscription, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/subscriptions/{subscriptionId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function editSubscriptionAdmin(int $subscriptionId, Request $request)
    {
        $subscription = $this->subscriptionService->getSubscription($subscriptionId);
        if (!$this->isCsrfTokenValid('subscription', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        //Integrate stripe id here
        if (empty($request->request->get('stripeId'))) {
            $stripe = new StripeClient($this->getParameter('secret_key'));
            $stripePrice = $this->getStripePrice($subscription, $stripe);
            $request->request->set('stripeId', $stripePrice['id']);
        }

        if (!$subscription) {
            throw new EntityNotFoundException('Subscription with id ' . $subscriptionId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(SubscriptionType::class, $subscription, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }

        $this->entityManager->persist($subscription);
        $this->entityManager->flush($subscription);

        $ressourceLocation = $this->generateUrl('subscription_admin_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/subscriptions/{subscriptionId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function deleteSubscriptions(int $subscriptionId): View
    {
        try {
            $this->subscriptionService->deleteSubscription($subscriptionId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('subscription_admin_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_subscriptions"})
     * @Rest\Get("subscription/{subscriptionId}")
     *
     * @return View
     */
    public function getSubscription(int $subscriptionId): View
    {
        $subscription = $this->subscriptionService->getSubscription($subscriptionId);

        return View::create($subscription, Response::HTTP_OK);
    }

    /**
     * @Rest\View(serializerGroups={"api_subscriptions"})
     * @Rest\Get("/subscribes")
     *
     * @return View
     */
    public function getSubscriptions(): View
    {
        $subscription = $this->subscriptionService->getAllSubscriptions();
        return View::create($subscription, Response::HTTP_OK);
    }
    /**
     * @Rest\View(serializerGroups={"api_subscriptions"})
     * @Rest\Get("/subscribes/{slug}")
     *
     * @return View
     */
    public function getSubscriptionByName(string $slug): View
    {
        $subscription = $this->subscriptionService->getSubscriptionByName($slug);
        return View::create($subscription, Response::HTTP_OK);
    }

    private function getStripePrice(Subscription $subscription, $stripe)
    {
        $subscriptionStripeId = $subscription->getStripeId();
        $price = null;
        if (empty($subscriptionStripeId)) {
            $product = $stripe->products->create([
                'name' => $subscription->getName(),
            ]);
            $price = $stripe->prices->create([
                'unit_amount' => $subscription->getPrice() * 100,
                'currency' => 'eur',
                'recurring' => ['interval' => 'month'],
                'product' => $product['id']
            ]);
            $subscription->setStripeId($price['id']);

            $this->entityManager->persist($subscription);
            $this->entityManager->flush();
        } else {
            $price = $stripe->prices->retrieve($subscriptionStripeId);
        }
        return $price;
    }
}
