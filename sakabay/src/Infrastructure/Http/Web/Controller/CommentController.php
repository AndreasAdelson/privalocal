<?php

namespace App\Infrastructure\Http\Web\Controller;

use App\Application\Service\CompanyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CommentController extends AbstractController
{

    private $companyService;

    /**
     * CompanyWebController constructor.
     */
    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }
    // /**
    //  * @Route("admin/comment", name="comment_index", methods="GET")
    //  * @Security("is_granted('ROLE_ADMIN')")
    //  */
    // public function index(AuthorizationCheckerInterface $authorizationChecker)
    // {
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    //     return $this->render('admin/comment/index.html.twig', [
    //         'canCreate' => $authorizationChecker->isGranted('ROLE_ADMIN'),
    //         'canRead' => $authorizationChecker->isGranted('ROLE_ADMIN'),
    //         'canEdit' => $authorizationChecker->isGranted('ROLE_ADMIN'),
    //         'canDelete' => $authorizationChecker->isGranted('ROLE_ADMIN'),
    //     ]);
    // }

    /**
     * @Route("comment/new/{slug}", name="comment_new", methods="GET|POST")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(string $slug)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $company = $this->companyService->getCompanyByUrlName($slug);
        $utilisateurId = $this->getUser()->getId();
        if ($utilisateurId === $company->getUtilisateur()->getId()) {
            return $this->redirectToRoute('company_show', ['slug' => $slug, 'page' => 'comments']);
        }
        return $this->render('comment/form.html.twig', [
            'utilisateurId' => $this->getUser()->getId(),
            'urlPrecedente' => $this->urlPrecedente(),
            'companyId' => $company->getId()
        ]);
    }

    // /**
    //  * @Route("admin/comment/edit/{id}", name="comment_edit", methods="GET|POST")
    //  * @Security("is_granted('ROLE_ADMIN')")
    //  */
    // public function edit(int $id)
    // {
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    //     return $this->render('admin/comment/form.html.twig', [
    //         'commentId' => $id,
    //     ]);
    // }

    // /**
    //  * @Security("is_granted('ROLE_ADMIN')")
    //  * @Route("admin/comment/{id}", name="comment_show", methods="GET|POST")
    //  */
    // public function show(int $id, AuthorizationCheckerInterface $authorizationChecker)
    // {
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    //     return $this->render('admin/comment/show.html.twig', [
    //         'canEdit' => $authorizationChecker->isGranted('ROLE_ADMIN'),
    //         'commentId' => $id,
    //         'urlPrecedente' => $this->urlPrecedente()
    //     ]);
    // }

    private function urlPrecedente()
    {
        $urlPrecedente = "/";
        if (isset($_SERVER['HTTP_REFERER'])) {
            $urlPrecedente = $_SERVER['HTTP_REFERER'];
        }
        return $urlPrecedente;
    }
}
