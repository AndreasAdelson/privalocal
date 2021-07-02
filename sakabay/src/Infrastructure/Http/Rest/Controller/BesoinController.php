<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\BesoinExpirateType;
use App\Application\Form\Type\BesoinType;
use App\Application\Form\Type\BesoinValidateType;
use App\Application\Form\Type\CommentValidateType;
use App\Application\Service\BesoinService;
use App\Application\Service\BesoinStatutService;
use App\Application\Service\CompanyService;
use App\Domain\Model\Besoin;
use App\Domain\Model\Comment;
use App\Infrastructure\Factory\NotificationFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Context\Context;
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

final class BesoinController extends AbstractFOSRestController
{
    private $entityManager;
    private $besoinService;
    private $besoinStatutService;
    private $translator;
    private $notificationFactory;
    private $companyService;


    /**
     * BesoinRestController constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        BesoinService $besoinService,
        TranslatorInterface $translator,
        BesoinStatutService $besoinStatutService,
        NotificationFactory $notificationFactory,
        CompanyService $companyService
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->besoinService = $besoinService;
        $this->besoinStatutService = $besoinStatutService;
        $this->notificationFactory = $notificationFactory;
        $this->companyService = $companyService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/besoins")
     * @param Request $request
     *
     * @return View
     */
    public function createBesoin(Request $request)
    {
        $besoin = new Besoin();
        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(BesoinType::class, $besoin, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $besoinsStatut = $this->besoinStatutService->getBesoinStatutByCode('PUB');
        $besoin->setBesoinStatut($besoinsStatut);
        $this->entityManager->persist($besoin);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('service_list');
        $this->notificationFactory->createService([$besoin->getAuthor()], $ressourceLocation, $besoin);

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_besoins"})
     * @Rest\Get("/admin/besoins")
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

    public function getBesoins(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');

        $pager = $this->besoinService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
        $besoins = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($besoins);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }

    /**
     * @Rest\View(serializerGroups={"api_besoins"})
     * @Rest\Get("besoins/{besoinId}")
     *
     * @return View
     */
    public function getBesoin(int $besoinId): View
    {
        $besoin = $this->besoinService->getBesoin($besoinId);

        return View::create($besoin, Response::HTTP_OK);
    }

    /**
     * @Rest\View(serializerGroups={"api_besoins_utilisateur"})
     * @Rest\Get("besoins/utilisateur/{utilisateurId}")
     *
     * @QueryParam(name="codeStatut",
     *             default="",
     *             description="Trie par code Statut"
     * )
     * @QueryParam(name="company",
     *             default="",
     *             description="Trie par entreprise auteur"
     * )
     * @QueryParam(name="currentPage",
     *             default="1",
     *             description="Page courante"
     * )
     * @QueryParam(name="perPage",
     *             default="10",
     *             description="Taille de la page"
     * )
     *
     * @QueryParam(name="count",
     *             default="false",
     *             description="Avoir le total de résultats"
     * )
     *
     * @return View
     */
    public function getBesoinByUser(int $utilisateurId, ParamFetcher $paramFetcher): View
    {
        $codeStatut = $paramFetcher->get('codeStatut');
        $company = $paramFetcher->get('company');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');
        $isCounting = $paramFetcher->get('count');

        /**
         * Oblige à ce que le paramètre utilisateur soit donné pour effectuer la requête.
         * Sans cela un petit malin pourrait récupérer tous les besoins depuis cette route.
         */
        if (empty($utilisateurId)) {
            throw new NotFoundHttpException('Bad request');
        }
        //Avoir le total
        if ($isCounting === 'true') {
            $response = $this->besoinService
                ->getCountBesoinByUserId($utilisateurId, $codeStatut, $company, $isCounting);
            return View::create($response, Response::HTTP_OK);
        }

        $pager = $this->besoinService
            ->getPaginatedBesoinByUserId($utilisateurId, $codeStatut, $company, $currentPage, $perPage);
        $besoins = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $view = $this->view($besoins, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
        // $besoin = $this->besoinService->getBesoinByUserId($utilisateurId, $codeStatut);

        // return View::create($besoin, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("besoins/{besoinId}")
     *
     * @return View
     */
    public function editBesoin(int $besoinId, Request $request)
    {
        $besoin = $this->besoinService->getBesoin($besoinId);

        if (!$besoin) {
            throw new EntityNotFoundException('Besoin with id ' . $besoinId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(BesoinType::class, $besoin, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($besoin);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('service_list');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Post("besoins/{besoinId}/validate")
     *
     * @return View
     */
    public function editBesoinWithCompanyFounded(int $besoinId, Request $request)
    {
        $besoin = $this->besoinService->getBesoin($besoinId);

        if (!$besoin) {
            throw new EntityNotFoundException('Besoin with id ' . $besoinId . ' does not exist!');
        }
        $besoinStatut = $besoin->getBesoinStatut()->getCode();
        if ($besoinStatut !== 'PUB') {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_publish_besoin')),
            ]);
        }
        $commentDatas = $request->request->get('comment');
        $formOptions = [
            'translator' => $this->translator,
        ];
        if (!empty($commentDatas)) {
            $request->request->remove('comment');
        }

        $form = $this->createForm(BesoinValidateType::class, $besoin, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }

        if (!empty($commentDatas)) {
            $comment = new Comment();
            $formComment = $this->createForm(CommentValidateType::class, $comment, $formOptions);
            $formComment->submit($commentDatas);
            if (!$formComment->isValid()) {
                return $formComment;
            }
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $companyId = $comment->getCompany()->getId();
            $this->companyService->updateNotation($companyId);
        }

        $this->entityManager->persist($besoin);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('service_list');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Post("besoins/{besoinId}/expirate")
     *
     * @return View
     */
    public function editBesoinWithNoCompany(int $besoinId, Request $request)
    {
        $besoin = $this->besoinService->getBesoin($besoinId);

        if (!$besoin) {
            throw new EntityNotFoundException('Besoin with id ' . $besoinId . ' does not exist!');
        }

        $besoinStatut = $besoin->getBesoinStatut()->getCode();
        if ($besoinStatut !== 'PUB') {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_publish_besoin')),
            ]);
        }
        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(BesoinExpirateType::class, $besoin, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($besoin);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('service_list');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("/besoins/{besoinId}")
     *
     * @return View
     */
    public function deleteBesoin(int $besoinId): View
    {
        $besoin = $this->besoinService->getBesoin($besoinId);
        if (!empty($besoin->getAnswers())) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_delete_besoin')),
            ]);
        }
        try {
            $this->besoinService->deleteBesoin($besoinId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('service_list');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_besoins"})
     * @Rest\Get("/opportunities")
     *
     * @QueryParam(name="category",
     *             default="",
     *             description="Category de l'entreprise"
     * )
     * @QueryParam(name="sousCategory",
     *             default="",
     *             description="sousCategorys de l'entreprise"
     * )
     * @QueryParam(name="company",
     *             default="false",
     *             description="Uniquement les besoins provenant d'entreprises"
     * )
     * @QueryParam(name="currentPage",
     *             default="1",
     *             description="Page courante"
     * )
     * @QueryParam(name="perPage",
     *             default="10",
     *             description="Taille de la page"
     * )
     *
     * @QueryParam(name="count",
     *             default="false",
     *             description="Avoir le total de résultats"
     * )
     *
     * @return View
     */

    public function getOpportunities(ParamFetcher $paramFetcher): View
    {
        $category = $paramFetcher->get('category');
        $sousCategory = $paramFetcher->get('sousCategory');
        $company = $paramFetcher->get('company');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');
        $isCounting = $paramFetcher->get('count');
        /**
         * Oblige à ce que le paramètre category soit donné pour effectuer la requête.
         * Sans cela un petit malin pourrait récupérer tous les besoins depuis cette route.
         */
        if (empty($category)) {
            throw new NotFoundHttpException('Bad request');
        }
        //Avoir le total
        if ($isCounting === 'true') {
            $response = $this->besoinService
                ->getCountOpportunities($category, $sousCategory, $isCounting, $company);
            return View::create($response, Response::HTTP_OK);
        }
        $pager = $this->besoinService
            ->getPaginatedOpportunityList($category, $sousCategory, $company, $currentPage, $perPage);
        $besoins = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $view = $this->view($besoins, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }

    /**
     * @Rest\View(serializerGroups={"api_besoins"})
     * @Rest\Get("/opportunities/quote")
     *
     * @QueryParam(name="company",
     *             default="",
     *             description="Identifiant de l'entreprise"
     * )
     * @QueryParam(name="currentPage",
     *             default="1",
     *             description="Page courante"
     * )
     * @QueryParam(name="perPage",
     *             default="10",
     *             description="Taille de la page"
     * )
     * @QueryParam(name="count",
     *             default=false,
     *             description="Avoir le total de résultats"
     * )
     * @QueryParam(name="onlyCompany",
     *             default="false",
     *             description="Uniquement les devis des entreprises"
     * )
     *
     * @return View
     */

    public function getOpportunitiesWithRequestedQuote(ParamFetcher $paramFetcher): View
    {
        $company = $paramFetcher->get('company');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');
        $isCounting = $paramFetcher->get('count');
        $onlyCompany = $paramFetcher->get('onlyCompany');
        /**
         * Oblige à ce que le paramètre company soit donné pour effectuer la requête.
         * Sans cela un petit malin pourrait récupérer tous les besoins depuis cette route.
         */
        if (empty($company)) {
            throw new NotFoundHttpException('Bad request');
        }
        //Avoir le total
        if ($isCounting) {
            $response = $this->besoinService
                ->getCountOpportunitiesWithRequestedQuote($company, $isCounting, $onlyCompany);
            return View::create($response, Response::HTTP_OK);
        }
        $pager = $this->besoinService
            ->getPaginatedOpportunityWithRequestedQuoteList($company, $currentPage, $perPage, $onlyCompany);
        $besoins = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $view = $this->view($besoins, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }

    /**
     * @Rest\View(serializerGroups={"api_besoins"})
     * @Rest\Get("opportunities/recap/{besoinId}/{companyId}")
     *
     * @return View
     */
    public function getOpportunityRecap(int $besoinId, int $companyId): View
    {
        $besoin = $this->besoinService->getBesoinAnsweredByCompany($besoinId, $companyId);

        return View::create($besoin, Response::HTTP_OK);
    }
}
