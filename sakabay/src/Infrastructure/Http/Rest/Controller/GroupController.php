<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\GroupType;
use App\Application\Service\GroupService;
use App\Domain\Model\Group;
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

final class GroupController extends AbstractFOSRestController
{
    private $entityManager;
    private $roleService;
    private $translator;

    /**
     * GroupRestController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, GroupService $groupService, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->groupService = $groupService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/groups")
     * @Security("is_granted('ROLE_CGROUP')")
     * @param Request $request
     *
     * @return View
     */
    public function createGroup(Request $request)
    {
        $group = new Group();
        if (!$this->isCsrfTokenValid('group', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(GroupType::class, $group, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($group);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('group_index');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_groups"})
     * @Rest\Get("/admin/groups")
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

    public function getGroups(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');

        $pager = $this->groupService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
        $groups = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($groups);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }
    /**
     * @Rest\View(serializerGroups={"api_groups"})
     * @Rest\Get("admin/groups/{groupId}")
     *
     * @return View
     */
    public function getGroup(int $groupId): View
    {
        $group = $this->groupService->getGroup($groupId);

        return View::create($group, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/groups/{groupId}")
     * @Security("is_granted('ROLE_UGROUP')")
     *
     * @return View
     */
    public function editGroup(int $groupId, Request $request)
    {
        $group = $this->groupService->getGroup($groupId);
        if (!$this->isCsrfTokenValid('group', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$group) {
            throw new EntityNotFoundException('Group with id ' . $groupId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(GroupType::class, $group, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($group);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('group_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/groups/{groupId}")
     * @Security("is_granted('ROLE_DGROUP')")
     *
     * @return View
     */
    public function deleteGroups(int $groupId): View
    {
        try {
            $this->groupService->deleteGroup($groupId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('group_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }
}
