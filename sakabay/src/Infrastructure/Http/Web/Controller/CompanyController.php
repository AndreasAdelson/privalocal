<?php

namespace App\Infrastructure\Http\Web\Controller;

use App\Application\Service\CompanyService;
use App\Application\Service\CompanyStatutService;
use App\Domain\Model\Company;
use App\Infrastructure\Repository\CompanyStatutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CompanyController extends AbstractController
{
    private $companyService;
    private $companyStatutService;

    /**
     * CompanyWebController constructor.
     */
    public function __construct(CompanyService $companyService, CompanyStatutService $companyStatutService)
    {
        $this->companyService = $companyService;
        $this->companyStatutService = $companyStatutService;
    }
    /**
     * @Route("entreprises/search", name="company_search", methods="GET")
     */
    public function searchIndex()
    {
        return $this->render('company/search.html.twig', []);
    }


    /**
     * @Route("entreprise", name="company_register_index", methods="GET")
     */
    public function registerIndex(AuthorizationCheckerInterface $authorizationChecker)
    {
        return $this->render('company/index.html.twig', []);
    }

    /**
     * @Route("entreprise/new", name="company_admin_new", methods="GET|POST")
     */
    public function new()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('company/form.html.twig', [
            'utilisateurId' => $this->getUser()->getId(),
            'urlPrecedente' => $this->urlPrecedente()
        ]);
    }

    /**
     * @Route("entreprise/{slug}", name="company_show", methods="GET")
     */
    public function show(string $slug)
    {
        $company = $this->companyService->getCompanyByUrlName($slug);
        if (!$company) {
            throw new NotFoundHttpException('Company with url_name ' . $slug . ' does not exist!');
        } elseif ($company->getCompanystatut()->getCode() !== "VAL") {
            throw $this->createNotFoundException("This company does not exist");
        }
        $isSubscriptionActive = $this->companyService->isCompanySubscribtionActive($company);
        $titlePage = $company->getName();
        if (!empty($this->getUser())) {
            $utilisateurId = $this->getUser()->getId();
        } else {
            $utilisateurId = 0;
        }
        return $this->render('company/show.html.twig', [
            'companyUrlName' => $slug,
            'titlePage' => $titlePage,
            'isSubscriptionActive' => $isSubscriptionActive,
            'utilisateurId' => $utilisateurId
        ]);
    }

    /**
     * @Route("admin/entreprise", name="company_validated_index", methods="GET")
     */
    public function validatedIndex(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('admin/company/validated/validated_index.html.twig', [
            'canEdit' => $authorizationChecker->isGranted('ROLE_UCOMPANY'),
            'canDelete' => $authorizationChecker->isGranted('ROLE_DCOMPANY'),
            'canRead' => $authorizationChecker->isGranted('ROLE_RCOMPANY')
        ]);
    }

    /**
     * @Route("admin/entreprise/edit/{id}", name="company_validated_edit", methods="GET|POST")
     */
    public function editValidated(int $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('admin/company/validated/validated_form.html.twig', [
            'companyId' => $id,
            'urlPrecedente' => $this->urlPrecedente(),
            'page' => CompanyStatutRepository::VALIDE_CODE
        ]);
    }

    /**
     * @Route("admin/entreprise/{id}", name="company_validated_show", methods="GET")
     */
    public function showValidated(int $id, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('admin/company/validated/validated_show.html.twig', [
            'companyId' => $id,
            'canEdit' => $authorizationChecker->isGranted('ROLE_UCOMPANY'),
            'canEditSubscription' => $authorizationChecker->isGranted('ROLE_UCOMPANYSUBSCRIPTION'),
            'urlPrecedente' => $this->urlPrecedente()
        ]);
    }

    /**
     * @Route("admin/registered/entreprise", name="company_registered_index", methods="GET")
     */
    public function registeredIndex(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('admin/company/registered/registered_index.html.twig', [
            'canEdit' => $authorizationChecker->isGranted('ROLE_UCOMPANY'),
            'canDelete' => $authorizationChecker->isGranted('ROLE_DCOMPANY'),
            'canRead' => $authorizationChecker->isGranted('ROLE_RCOMPANY')
        ]);
    }

    /**
     * @Route("admin/registered/entreprise/edit/{id}", name="company_registered_edit", methods="GET|POST")
     */
    public function editRegistered(int $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('admin/company/registered/registered_form.html.twig', [
            'companyId' => $id,
            'urlPrecedente' => $this->urlPrecedente(),
            'page' => CompanyStatutRepository::EN_COURS_CODE
        ]);
    }


    /**
     * @Route("admin/registered/entreprise/{id}", name="company_registered_show", methods="GET")
     */
    public function showRegistered(int $id, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('admin/company/registered/registered_show.html.twig', [
            'companyId' => $id,
            'canEdit' => $authorizationChecker->isGranted('ROLE_UCOMPANY'),
            'urlPrecedente' => $this->urlPrecedente()
        ]);
    }

    /**
     * @Route("admin/refused/entreprise", name="company_refused_index", methods="GET")
     */
    public function refusedIndex(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('admin/company/refused/refused_index.html.twig', [
            'canEdit' => $authorizationChecker->isGranted('ROLE_UCOMPANY'),
            'canDelete' => $authorizationChecker->isGranted('ROLE_DCOMPANY'),
            'canRead' => $authorizationChecker->isGranted('ROLE_RCOMPANY')
        ]);
    }

    /**
     * @Route("admin/refused/entreprise/edit/{id}", name="company_refused_edit", methods="GET|POST")
     */
    public function editRefused(int $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('admin/company/refused/refused_form.html.twig', [
            'companyId' => $id,
            'urlPrecedente' => $this->urlPrecedente(),
            'page' => CompanyStatutRepository::REFUSED_CODE
        ]);
    }


    /**
     * @Route("admin/refused/entreprise/{id}", name="company_refused_show", methods="GET")
     */
    public function showRefused(int $id, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('admin/company/refused/refused_show.html.twig', [
            'companyId' => $id,
            'canEdit' => $authorizationChecker->isGranted('ROLE_UCOMPANY'),
            'urlPrecedente' => $this->urlPrecedente()
        ]);
    }

    /**
     * @Route("entreprises/list", name="company_list", methods="GET")
     */
    public function manageCompanyList()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $utilisateurId = $this->getUser()->getId();
        $companies = $this->getUser()->getCompanys();
        if (empty($companies)) {
            throw new NotFoundHttpException('Page does not exist');
        }
        return $this->render('company/list/index.html.twig', [
            'utilisateurId' => $utilisateurId,
        ]);
    }

    /**
     * @Route("entreprises/edit/{slug}", name="company_edit", methods="GET")
     */
    public function editOwnCompany(string $slug)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $company = $this->companyService->getCompanyByUrlName($slug);
        $ownerId = $company->getUtilisateur()->getId();
        $utilisateurId = $this->getUser()->getId();
        if ($ownerId != $utilisateurId || $company->getCompanystatut()->getCode() != 'VAL') {
            throw new NotFoundHttpException('Page does not exist');
        }
        $isSubscriptionActive = $this->companyService->isCompanySubscribtionActive($company);

        return $this->render('company/list/form.html.twig', [
            'utilisateurId' => $utilisateurId,
            'urlPrecedente' => $this->urlPrecedente(),
            'companyUrlName' => $slug,
            'isSubscriptionActive' => $isSubscriptionActive
        ]);
    }

    private function urlPrecedente()
    {
        $urlPrecedente = "/";
        if (isset($_SERVER['HTTP_REFERER'])) {
            $urlPrecedente = $_SERVER['HTTP_REFERER'];
        }
        return $urlPrecedente;
    }
}
