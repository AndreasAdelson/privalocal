<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\SousCategoryType;
use App\Application\Service\SousCategoryService;
use App\Domain\Model\SousCategory;
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

final class SousCategoryController extends AbstractFOSRestController
{
    private $entityManager;
    private $sousCategoryService;
    private $translator;

    /**
     * SousCategoryRestController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, SousCategoryService $sousCategoryService, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->sousCategoryService = $sousCategoryService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/sous-categories")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     *
     * @return View
     */
    public function createSousCategory(Request $request)
    {
        $sousCategory = new SousCategory();
        if (!$this->isCsrfTokenValid('sousCategory', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(SousCategoryType::class, $sousCategory, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($sousCategory);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('sous_category_index');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_sous_categories"})
     * @Rest\Get("/admin/sous-categories")
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
     * @QueryParam(name="category",
     *             default="",
     *             description="Identifiant catégorie"
     * )
     * @return View
     */

    public function getCategories(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');
        $category = $paramFetcher->get('category');

        $pager = $this->sousCategoryService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage, $category);
        $SousCategories = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($SousCategories);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }
    /**
     * @Rest\View(serializerGroups={"api_sous_categories"})
     * @Rest\Get("admin/sous-categories/{sousCategoryId}")
     *
     * @return View
     */
    public function getSousCategory(int $sousCategoryId): View
    {
        $sousCategory = $this->sousCategoryService->getSousCategory($sousCategoryId);

        return View::create($sousCategory, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/sous-categories/{sousCategoryId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function editSousCategory(int $sousCategoryId, Request $request)
    {
        $sousCategory = $this->sousCategoryService->getSousCategory($sousCategoryId);
        if (!$this->isCsrfTokenValid('sousCategory', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$sousCategory) {
            throw new EntityNotFoundException('SousCategory with id ' . $sousCategoryId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(SousCategoryType::class, $sousCategory, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($sousCategory);
        $this->entityManager->flush($sousCategory);

        $ressourceLocation = $this->generateUrl('sous_category_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/sous-categories/{sousCategoryId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function deleteCategories(int $sousCategoryId): View
    {
        try {
            $this->sousCategoryService->deleteSousCategory($sousCategoryId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('sous_category_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }
}
