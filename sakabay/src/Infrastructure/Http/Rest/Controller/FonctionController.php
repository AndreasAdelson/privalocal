<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\FonctionType;
use App\Application\Service\FonctionService;
use App\Domain\Model\Fonction;
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


final class FonctionController extends AbstractFOSRestController
{
    private $entityManager;
    private $fonctionService;
    private $translator;

    /**
     * FonctionRestController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, FonctionService $fonctionService, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->fonctionService = $fonctionService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/fonctions")
     * @Security("is_granted('ROLE_CFONCTION')")
     * @param Request $request
     *
     * @return View
     */
    public function createFonction(Request $request)
    {
        $fonction = new Fonction();
        if (!$this->isCsrfTokenValid('fonction', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(FonctionType::class, $fonction, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($fonction);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('fonction_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_fonctions"})
     * @Rest\Get("/admin/fonctions")
     *
     * @QueryParam(name="filterFields",
     *             default="description",
     *             description="Liste des champs sur lesquels le filtre s'appuie"
     * )
     * @QueryParam(name="filter",
     *             default="",
     *             description="Filtre"
     * )
     * @QueryParam(name="sortBy",
     *             default="description",
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
    public function getFonctions(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');

        $pager = $this->fonctionService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
        $fonctions = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($fonctions);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }

    /**
     * @Rest\View(serializerGroups={"api_fonction"})
     * @Rest\Get("admin/fonctions/{fonctionId}")
     *
     * @return View
     */
    public function getFonction(int $fonctionId): View
    {
        $fonction = $this->fonctionService->getFonction($fonctionId);

        return View::create($fonction, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/fonctions/edit/{fonctionId}")
     *@Security("is_granted('ROLE_UFONCTION')")
     * @return View
     */
    public function editFonction(int $fonctionId, Request $request)
    {
        $fonction = $this->fonctionService->getFonction($fonctionId);
        if (!$this->isCsrfTokenValid('fonction', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$fonction) {
            throw new EntityNotFoundException('Fonction with id ' . $fonctionId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(FonctionType::class, $fonction, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($fonction);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('fonction_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/fonctions/{fonctionId}")
     * @Security("is_granted('ROLE_DFONCTION')")
     *
     * @return View
     */
    public function deleteFonctions(int $fonctionId): View
    {
        try {
            $fonction = $this->fonctionService->deleteFonction($fonctionId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('fonction_index');

        return View::create($fonction, Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }
}
