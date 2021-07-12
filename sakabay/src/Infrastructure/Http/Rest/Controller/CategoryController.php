<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\CategoryType;
use App\Application\Service\CategoryService;
use App\Domain\Model\Category;
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

final class CategoryController extends AbstractFOSRestController
{
    private $entityManager;
    private $categoryService;
    private $translator;

    /**
     * CategoryRestController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, CategoryService $categoryService, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->categoryService = $categoryService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/categories")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     *
     * @return View
     */
    public function createCategory(Request $request)
    {
        $category = new Category();
        if (!$this->isCsrfTokenValid('category', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(CategoryType::class, $category, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('category_index');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_categories"})
     * @Rest\Get("/admin/categories")
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

    public function getCategories(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');

        $pager = $this->categoryService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
        $categories = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($categories);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }
    /**
     * @Rest\View(serializerGroups={"api_categories"})
     * @Rest\Get("admin/categories/{categoryId}")
     *
     * @return View
     */
    public function getCategory(int $categoryId): View
    {
        $category = $this->categoryService->getCategory($categoryId);
        if (!$category) {
            throw new EntityNotFoundException('Category with id ' . $categoryId . ' does not exist!');
        }
        return View::create($category, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/categories/{categoryId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function editCategory(int $categoryId, Request $request)
    {
        $category = $this->categoryService->getCategory($categoryId);
        if (!$this->isCsrfTokenValid('category', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$category) {
            throw new EntityNotFoundException('Category with id ' . $categoryId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(CategoryType::class, $category, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush($category);

        $ressourceLocation = $this->generateUrl('category_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/categories/{categoryId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function deleteCategories(int $categoryId): View
    {
        try {
            $this->categoryService->deleteCategory($categoryId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('category_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }
}
