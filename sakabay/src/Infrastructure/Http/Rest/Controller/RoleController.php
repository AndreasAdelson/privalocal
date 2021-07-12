<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\RoleType;
use App\Application\Service\RoleService;
use App\Domain\Model\Role;
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

final class RoleController extends AbstractFOSRestController
{
    private $entityManager;
    private $roleService;
    private $translator;

    /**
     * RoleRestController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, RoleService $roleService, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->roleService = $roleService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/roles")
     * @Security("is_granted('ROLE_CROLE')")
     * @param Request $request
     *
     * @return View
     */
    public function createRole(Request $request)
    {
        $role = new Role();
        if (!$this->isCsrfTokenValid('role', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(RoleType::class, $role, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($role);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('role_index');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_roles"})
     * @Rest\Get("/admin/roles")
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

    public function getRoles(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');

        $pager = $this->roleService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
        $roles = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($roles);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }
    /**
     * @Rest\View(serializerGroups={"api_roles"})
     * @Rest\Get("admin/roles/{roleId}")
     *
     * @return View
     */
    public function getRole(int $roleId): View
    {
        $role = $this->roleService->getRole($roleId);

        return View::create($role, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/roles/{roleId}")
     * @Security("is_granted('ROLE_UROLE')")
     *
     * @return View
     */
    public function editRole(int $roleId, Request $request)
    {
        $role = $this->roleService->getRole($roleId);
        if (!$this->isCsrfTokenValid('role', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$role) {
            throw new EntityNotFoundException('Role with id ' . $roleId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(RoleType::class, $role, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($role);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('role_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/roles/{roleId}")
     * @Security("is_granted('ROLE_DROLE')")
     *
     * @return View
     */
    public function deleteRoles(int $roleId): View
    {
        try {
            $this->roleService->deleteRole($roleId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('role_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }
}
