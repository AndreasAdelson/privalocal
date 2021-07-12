<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\AdvantageType;
use App\Application\Service\AdvantageService;
use App\Domain\Model\Advantage;
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

final class AdvantageController extends AbstractFOSRestController
{
    private $entityManager;
    private $advantageService;
    private $translator;

    /**
     * AdvantageRestController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, AdvantageService $advantageService, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->advantageService = $advantageService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/advantages")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     *
     * @return View
     */
    public function createAdvantage(Request $request)
    {
        $advantage = new Advantage();
        if (!$this->isCsrfTokenValid('advantage', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(AdvantageType::class, $advantage, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($advantage);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('advantage_index');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_advantages"})
     * @Rest\Get("/admin/advantages")
     *
     * @QueryParam(name="filterFields",
     *             default="message",
     *             description="Liste des champs sur lesquels le filtre s'appuie"
     * )
     * @QueryParam(name="filter",
     *             default="",
     *             description="Filtre"
     * )
     * @QueryParam(name="sortBy",
     *             default="priority",
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

    public function getAdvantages(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');

        $pager = $this->advantageService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
        $advantages = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($advantages);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }
    /**
     * @Rest\View(serializerGroups={"api_advantages"})
     * @Rest\Get("admin/advantages/{advantageId}")
     *
     * @return View
     */
    public function getAdvantage(int $advantageId): View
    {
        $advantage = $this->advantageService->getAdvantage($advantageId);
        if (!$advantage) {
            throw new EntityNotFoundException('Advantage with id ' . $advantageId . ' does not exist!');
        }
        return View::create($advantage, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/advantages/{advantageId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function editAdvantage(int $advantageId, Request $request)
    {
        $advantage = $this->advantageService->getAdvantage($advantageId);
        if (!$this->isCsrfTokenValid('advantage', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$advantage) {
            throw new EntityNotFoundException('Advantage with id ' . $advantageId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(AdvantageType::class, $advantage, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }

        $this->entityManager->persist($advantage);
        $this->entityManager->flush($advantage);

        $ressourceLocation = $this->generateUrl('advantage_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/advantages/{advantageId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function deleteAdvantages(int $advantageId): View
    {
        try {
            $this->advantageService->deleteAdvantage($advantageId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('advantage_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }
}
