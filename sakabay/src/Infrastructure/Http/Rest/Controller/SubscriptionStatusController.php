<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\SubscriptionStatusType;
use App\Application\Service\SubscriptionStatusService;
use App\Domain\Model\SubscriptionStatus;
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SubscriptionStatusController extends AbstractFOSRestController
{
    private $entityManager;
    private $subscriptionStatusService;
    private $translator;

    /**
     * SubscriptionStatusRestController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, SubscriptionStatusService $subscriptionStatusService, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->subscriptionStatusService = $subscriptionStatusService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/subscription-status")
     * @Security("is_granted('ROLE_CROLE')")
     * @param Request $request
     *
     * @return View
     */
    public function createSubscriptionStatus(Request $request)
    {
        if (!$this->isCsrfTokenValid('subsriptionStatus', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $subscriptionStatus = new SubscriptionStatus();
        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(SubscriptionStatusType::class, $subscriptionStatus, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($subscriptionStatus);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('subscription_status_index');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_subscription_status"})
     * @Rest\Get("/admin/subscription-status")
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

    public function getSubscriptionStatuss(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');

        $pager = $this->subscriptionStatusService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
        $subscriptionStatuss = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($subscriptionStatuss);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }
    /**
     * @Rest\View(serializerGroups={"api_subscription_status"})
     * @Rest\Get("admin/subscription-status/{subscriptionStatusId}")
     *
     * @return View
     */
    public function getSubscriptionStatus(int $subscriptionStatusId): View
    {
        $subscriptionStatus = $this->subscriptionStatusService->getSubscriptionStatus($subscriptionStatusId);

        return View::create($subscriptionStatus, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/subscription-status/{subscriptionStatusId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function editSubscriptionStatus(int $subscriptionStatusId, Request $request)
    {
        $subscriptionStatus = $this->subscriptionStatusService->getSubscriptionStatus($subscriptionStatusId);
        if (!$this->isCsrfTokenValid('subsriptionStatus', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$subscriptionStatus) {
            throw new EntityNotFoundException('SubscriptionStatus with id ' . $subscriptionStatusId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(SubscriptionStatusType::class, $subscriptionStatus, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($subscriptionStatus);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('subscription_status_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/subscription-status/{subscriptionStatusId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function deleteSubscriptionStatuss(int $subscriptionStatusId): View
    {
        try {
            $this->subscriptionStatusService->deleteSubscriptionStatus($subscriptionStatusId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('subscription_status_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }
}
