<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\CompanyStatutType;
use App\Application\Service\CompanyStatutService;
use App\Domain\Model\CompanyStatut;
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

final class CompanyStatutController extends AbstractFOSRestController
{
    private $entityManager;
    private $companyStatutService;
    private $translator;

    /**
     * CompanyStatutRestController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, CompanyStatutService $companyStatutService, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->companyStatutService = $companyStatutService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/company-statuts")
     * @Security("is_granted('ROLE_CROLE')")
     * @param Request $request
     *
     * @return View
     */
    public function createCompanyStatut(Request $request)
    {
        $companyStatut = new CompanyStatut();
        if (!$this->isCsrfTokenValid('companyStatut', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(CompanyStatutType::class, $companyStatut, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($companyStatut);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('company_statut_index');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_company_statut"})
     * @Rest\Get("/admin/company-statuts")
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

    public function getCompanyStatuts(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');

        $pager = $this->companyStatutService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
        $companyStatuts = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($companyStatuts);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }
    /**
     * @Rest\View(serializerGroups={"api_company_statut"})
     * @Rest\Get("admin/company-statuts/{companyStatutId}")
     *
     * @return View
     */
    public function getCompanyStatut(int $companyStatutId): View
    {
        $companyStatut = $this->companyStatutService->getCompanyStatut($companyStatutId);

        return View::create($companyStatut, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/company-statuts/{companyStatutId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function editCompanyStatut(int $companyStatutId, Request $request)
    {
        $companyStatut = $this->companyStatutService->getCompanyStatut($companyStatutId);
        if (!$this->isCsrfTokenValid('companyStatut', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$companyStatut) {
            throw new EntityNotFoundException('CompanyStatut with id ' . $companyStatutId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(CompanyStatutType::class, $companyStatut, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($companyStatut);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('company_statut_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/company-statuts/{companyStatutId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function deleteCompanyStatuts(int $companyStatutId): View
    {
        try {
            $this->companyStatutService->deleteCompanyStatut($companyStatutId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('company_statut_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }
}
