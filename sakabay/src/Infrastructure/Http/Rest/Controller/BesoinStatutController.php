<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\BesoinStatutType;
use App\Application\Service\BesoinStatutService;
use App\Domain\Model\BesoinStatut;
use Doctrine\ORM\EntityManager;
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

final class BesoinStatutController extends AbstractFOSRestController
{
    private $entityManager;
    private $roleService;
    private $translator;

    /**
     * BesoinStatutRestController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, BesoinStatutService $besoinStatutService, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->besoinStatutService = $besoinStatutService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/besoin-statuts")
     * @Security("is_granted('ROLE_CGROUP')")
     * @param Request $request
     *
     * @return View
     */
    public function createBesoinStatut(Request $request)
    {
        $besoinStatut = new BesoinStatut();
        if (!$this->isCsrfTokenValid('besoinStatut', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(BesoinStatutType::class, $besoinStatut, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($besoinStatut);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('besoin_statut_index');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_besoin_statut"})
     * @Rest\Get("/admin/besoin-statuts")
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

    public function getBesoinStatuts(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');

        $pager = $this->besoinStatutService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
        $besoinStatuts = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($besoinStatuts);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }
    /**
     * @Rest\View(serializerGroups={"api_besoin_statut"})
     * @Rest\Get("admin/besoin-statuts/{besoinStatutId}")
     *
     * @return View
     */
    public function getBesoinStatut(int $besoinStatutId): View
    {
        $besoinStatut = $this->besoinStatutService->getBesoinStatut($besoinStatutId);

        return View::create($besoinStatut, Response::HTTP_OK);
    }

    /**
     * @Rest\View(serializerGroups={"api_besoin_statut"})
     * @Rest\Get("besoin-statuts")
     *
     * @return View
     */
    public function getBesoinStatutsWithoutPUB(): View
    {
        $besoinStatuts = $this->besoinStatutService->getBesoinStatutsWithoutPUB();

        return View::create($besoinStatuts, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/besoin-statuts/{besoinStatutId}")
     * @Security("is_granted('ROLE_UGROUP')")
     *
     * @return View
     */
    public function editBesoinStatut(int $besoinStatutId, Request $request)
    {
        $besoinStatut = $this->besoinStatutService->getBesoinStatut($besoinStatutId);
        if (!$this->isCsrfTokenValid('besoinStatut', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$besoinStatut) {
            throw new EntityNotFoundException('BesoinStatut with id ' . $besoinStatutId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(BesoinStatutType::class, $besoinStatut, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($besoinStatut);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('besoin_statut_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/besoin-statuts/{besoinStatutId}")
     * @Security("is_granted('ROLE_DGROUP')")
     *
     * @return View
     */
    public function deleteBesoinStatuts(int $besoinStatutId): View
    {
        try {
            $this->besoinStatutService->deleteBesoinStatut($besoinStatutId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('besoin_statut_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }
}
