<?php

namespace App\Infrastructure\Http\Rest\Controller;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Application\Form\Type\CompanyType;
use App\Application\Form\Type\CompanyAdminEditType;
use App\Application\Form\Type\CompanySubscriptionType;
use App\Application\Form\Type\CompanyUserEditType;
use App\Application\Service\CompanyStatutService;
use App\Application\Service\CompanyService;
use App\Application\Service\FileUploader;
use App\Application\Service\SubscriptionService;
use App\Application\Service\SubscriptionStatusService;
use App\Application\Service\UtilisateurService;
use App\Domain\Model\Company;
use App\Domain\Model\CompanySubscription;
use App\Infrastructure\Factory\NotificationFactory;
use App\Infrastructure\Repository\CompanyStatutRepository;
use App\Infrastructure\Repository\SubscriptionRepository;
use App\Infrastructure\Repository\SubscriptionStatusRepository;
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
use Symfony\Component\Routing\Generator\UrlGenerator;

final class CompanyController extends AbstractFOSRestController
{
    private $entityManager;
    private $companyService;
    private $companyStatutService;
    private $translator;
    private $notificationFactory;
    private $utilisateurService;
    private $subscriptionStatusService;
    private $subscriptionService;

    /**
     * CompanyRestController constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CompanyService $companyService,
        CompanyStatutService $companyStatutService,
        TranslatorInterface $translator,
        NotificationFactory $notificationFactory,
        UtilisateurService $utilisateurService,
        SubscriptionService $subscriptionService,
        SubscriptionStatusService $subscriptionStatusService
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->companyService = $companyService;
        $this->companyStatutService = $companyStatutService;
        $this->notificationFactory = $notificationFactory;
        $this->utilisateurService = $utilisateurService;
        $this->subscriptionService = $subscriptionService;
        $this->subscriptionStatusService = $subscriptionStatusService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/companies")
     * @param Request $request
     *
     * @return View
     */
    public function createCompany(Request $request, FileUploader $uploader, string $uploadDir)
    {
        $company = new Company();

        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(CompanyType::class, $company, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }

        $urlName  = $this->recastName($company->getName());
        $company->setUrlName($urlName);

        $companyStatut = $this->companyStatutService->getCompanyStatutByCode(CompanyStatutRepository::EN_COURS_CODE);
        $company->setCompanystatut($companyStatut);

        $this->entityManager->persist($company);
        $this->entityManager->flush();
        //WARNING be sure about who get this notification for production

        $email = $company->getEmail();
        $subject = $this->translator->trans('email_register_confirmation_subject');
        $bodyMessage = sprintf($this->translator->trans('email_register_confirmation_body'), $company->getName());
        $this->sendMail($email, $subject, $bodyMessage);

        //WARNING be sure about who get this notification for production
        $rights = [
            'groups' => ['SUADMIN'],
        ];
        //Admins notification
        $users = $this->utilisateurService->findUsersByRight($rights);
        $ressourceLocation = $this->generateUrl('company_registered_show', ['id' => $company->getId()]);
        $this->notificationFactory->createCompanyNotificationAdmin($users, $ressourceLocation, $company);

        //User->company notification
        $ressourceLocation = $this->generateUrl('company_list');
        $this->notificationFactory->createCompanyNotificationUser([$company->getUtilisateur()], $ressourceLocation, $company);

        $ressourceLocation = $this->generateUrl('dashboard');
        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }


    /**
     * @Rest\View(serializerGroups={"api_companies"})
     * @Rest\Get("/companies")
     *
     * - - - - - - - - - - - - Query group for paginated list view - - - - - - - - - - - - -
     * Retrieves sorted list of all companies
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     * @QueryParam(name="filterFields",
     *             default="name,description_clean",
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
     * @QueryParam(name="codeStatut",
     *             default="",
     *             description="Statut d'une entreprise"
     * )
     * @QueryParam(name="category",
     *             default="",
     *             description="Identifiant d'une catégorie"
     * )
     * @QueryParam(name="city",
     *             default="",
     *             description="Identifiant d'une ville"
     * )
     * @QueryParam(name="sousCategory",
     *             default="",
     *             description="Identifiant d'une ville"
     * )
     * @return View
     */

    public function getCompanys(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');
        $codeStatut = $paramFetcher->get('codeStatut');
        $category = $paramFetcher->get('category');
        $city = $paramFetcher->get('city');
        $sousCategory = $paramFetcher->get('sousCategory');

        $pager = $this->companyService
            ->getPaginatedListUser($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage, $codeStatut, $category, $city, $sousCategory);
        $companies = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $view = $this->view($companies, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }

    /**
     * @Rest\View(serializerGroups={"api_companies"})
     * @Rest\Get("companies/{companyId}")
     *
     * @return View
     */
    public function getCompany(int $companyId): View
    {
        $company = $this->companyService->getCompany($companyId);

        return View::create($company, Response::HTTP_OK);
    }

    /**
     * @Rest\View(serializerGroups={"api_admin_companies"})
     * @Rest\Get("admin/companies")
     *
     * - - - - - - - - - - - - Query group for paginated admin list - - - - - - - - - - - - -
     * Retrieves sorted list of all companies
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
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
     * @QueryParam(name="codeStatut",
     *             default="",
     *             description="Statut d'une entreprise"
     * )
     *
     * @QueryParam(name="category",
     *             default="",
     *             description="Identifiant d'une catégorie"
     * )
     * @return View
     */

    public function getAdminCompanys(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');
        $codeStatut = $paramFetcher->get('codeStatut');

        $pager = $this->companyService
            ->getPaginatedListAdmin($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage, $codeStatut);
        $companies = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($companies);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }

    /**
     * @Rest\View(serializerGroups={"api_admin_companies"})
     * @Rest\Get("admin/companies/{companyId}")
     *
     * @return View
     */
    public function getAdminCompany(int $companyId): View
    {
        $company = $this->companyService->getCompany($companyId);

        return View::create($company, Response::HTTP_OK);
    }


    /**
     * @Rest\View(serializerGroups={"api_admin_companies"})
     * @Rest\Get("entreprise/{urlName}")
     * @QueryParam(name="serial_group",
     *             nullable=false,
     *             default="api_companies",
     *             description="Un groupe de sérialisation parmis les groupes autorisés"
     * )
     * @return View
     */
    public function getCompanyByUrlName(string $urlName, ParamFetcher $paramFetcher): View
    {
        $company = $this->companyService->getCompanyByUrlName($urlName);
        if (!$company) {
            throw new NotFoundHttpException('Company with url_name ' . $urlName . ' does not exist!');
        }
        $authorizedSerialGroups = [
            'api_companies',
            'api_admin_companies'
        ];
        $querySerial = $paramFetcher->get('serial_group');
        if (in_array($querySerial, $authorizedSerialGroups)) {
            $serializerGroups = [$querySerial];
        } else {
            $serializerGroups = ['api_companies'];
        }
        $context = new Context();
        $context->setGroups($serializerGroups);
        return View::create($company, Response::HTTP_OK)->setContext($context);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/companies/{companyId}")
     * @Security("is_granted('ROLE_UCOMPANY')")
     *
     * @return View
     */
    public function editAdminCompany(int $companyId, Request $request)
    {
        $company = $this->companyService->getCompany($companyId);
        if (!$company) {
            throw new EntityNotFoundException('Company with id ' . $companyId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $isValidated =  $request->request->get('validated');
        $ressourceLocation = $isValidated ? $this->generateUrl('company_validated_index') : $this->generateUrl('company_registered_index');
        $request->request->remove('validated');

        $form = $this->createForm(CompanyAdminEditType::class, $company, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($company);
        $this->entityManager->flush($company);

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Post("companies/edit/{companyUrlName}")
     *
     * @return View
     */
    public function editUserCompany(string $companyUrlName, Request $request, string $uploadDir, FileUploader $uploader)
    {
        $company = $this->companyService->getCompanyByUrlName($companyUrlName);
        if (!$company) {
            throw new EntityNotFoundException('Company with url name ' . $companyUrlName . ' does not exist!');
        }
        $utilisateurId = $request->request->get('utilisateur');
        $ownerId = $company->getUtilisateur()->getId();
        if ($ownerId != $utilisateurId || $company->getCompanystatut()->getCode() != 'VAL') {
            throw $this->createAccessDeniedException();
        }
        $file = $request->files->get('file');

        if (!empty($file)) {
            $newFileName = $uploader->setFileName($file);
            $uploadDirectory = $uploadDir . '/' . $companyUrlName;
            $uploader->upload($uploadDirectory, $file, $newFileName);
            $request->request->set('imageProfil', $newFileName);
            $oldImage = $company->getImageProfil();
            if (!empty($oldImage)) {
                if (file_exists($oldImage)) {
                    $uploader->deleteOldFile($uploadDirectory, $oldImage);
                }
            }
        }
        $request->request->remove('utilisateur');
        $formOptions = [
            'translator' => $this->translator,
        ];
        $ressourceLocation = $this->generateUrl('company_list');


        $form = $this->createForm(CompanyUserEditType::class, $company, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($company);
        $this->entityManager->flush($company);

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/companies/{companyId}/validation")
     * @Security("is_granted('ROLE_UCOMPANY')")
     *
     * @return View
     */
    public function validateCompany(int $companyId, Request $request)
    {
        $company = $this->companyService->getCompany($companyId);

        if (!$company) {
            throw new EntityNotFoundException('Company with id ' . $companyId . ' does not exist!');
        }
        $companyStatut = $this->companyStatutService->getCompanyStatutByCode(CompanyStatutRepository::VALIDE_CODE);
        $company->setCompanyStatut($companyStatut);

        $this->entityManager->persist($company);
        $this->entityManager->flush($company);

        //2 months subscription offers
        $companySubscription = new CompanySubscription();
        $subscriptionStatus = $this->subscriptionStatusService->getSubscriptionStatusByCode(SubscriptionStatusRepository::OFFER_CODE)->getId();
        $subscription = $this->subscriptionService->getSubscriptionByCode(SubscriptionRepository::PREMIUM_CODE)->getId();
        $request->request->set('subscription', $subscription);
        $request->request->set('subscriptionStatus', $subscriptionStatus);

        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(CompanySubscriptionType::class, $companySubscription, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($companySubscription);
        $this->entityManager->flush($companySubscription);
        // WARNING carreful about the email address for production
        //Send email
        $email = $company->getUtilisateur()->getEmail();
        $subject = $this->translator->trans('email_register_validation_subject');
        $bodyMessage = sprintf($this->translator->trans('email_register_validation_body'), $company->getName(), $this->generateUrl('home'));
        $this->sendMail($email, $subject, $bodyMessage);

        $ressourceLocation = $this->generateUrl('company_validated_show', ['id' => $companyId], UrlGenerator::RELATIVE_PATH);

        //WARNING be sure about who get this notification for production
        $rights = [
            'groups' => ['SUADMIN'],
        ];
        //Admins notification
        $users = $this->utilisateurService->findUsersByRight($rights);
        $this->notificationFactory->validationCompanyNotificationAdmin($users, $ressourceLocation, $company);
        //User->company notification
        $ressourceLocation = $this->generateUrl('company_show', ['slug' => $company->getUrlName()]);
        $this->notificationFactory->validationCompanyNotificationUser([$company->getUtilisateur()], $ressourceLocation, $company);

        $ressourceLocation = $this->generateUrl('company_registered_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/companies/{companyId}")
     * @Security("is_granted('ROLE_DCOMPANY')")
     *
     * @return View
     */
    public function deleteCompany(int $companyId): View
    {
        try {
            $this->companyService->deleteCompany($companyId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('company_validated_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/companies/{companyId}/decline")
     * @Security("is_granted('ROLE_UCOMPANY')")
     *
     * @return View
     */
    public function declineCompany(int $companyId): View
    {
        $company = $this->companyService->getCompany($companyId);

        if (!$company) {
            throw new EntityNotFoundException('Company with id ' . $companyId . ' does not exist!');
        }
        $companyStatut = $this->companyStatutService->getCompanyStatutByCode(CompanyStatutRepository::REFUSED_CODE);
        $company->setCompanyStatut($companyStatut);

        $this->entityManager->persist($company);
        $this->entityManager->flush($company);

        // WARNING carreful about the email address for production

        //Send email
        // $email = $company->getUtilisateur()->getEmail();
        // $email = "andreasadelson@gmail.com";
        // $subject = $this->translator->trans('email_register_decline_subject');
        // $bodyMessage = sprintf($this->translator->trans('email_register_decline_body'), $company->getName(), $this->generateUrl('home'));
        // $this->sendMail($email, $subject, $bodyMessage);


        //WARNING be sure about who get this notification for production
        // $rights = [
        //     'groups' => ['SUADMIN'],
        // ];
        // //Admins notification
        // $users = $this->utilisateurService->findUsersByRight($rights);
        // $ressourceLocation = $this->generateUrl('company_subscribed_show', ['id' => $companyId], UrlGenerator::RELATIVE_PATH);
        // $this->notificationFactory->validationCompanyNotificationAdmin($users, $ressourceLocation, $company);

        // //User->company notification
        // $ressourceLocation = $this->generateUrl('company_show', ['slug' => $company->getUrlName()]);
        // $this->notificationFactory->validationCompanyNotificationUser([$company->getUtilisateur()], $ressourceLocation, $company);

        $ressourceLocation = $this->generateUrl('company_registered_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_admin_companies"})
     * @Rest\Get("/companies/utilisateur/{utilisateurId}")
     * @QueryParam(name="onlySubscribed",
     *             default="false",
     *             description="Avoir les entreprises abonnées uniquement"
     * )
     * @return View
     */
    public function getCompanyByUserId(ParamFetcher $paramFetcher, string $utilisateurId): View
    {
        $onlySubscribed = $paramFetcher->get('onlySubscribed');
        if ($onlySubscribed === 'true') {
            $companies = $this->companyService->getCompanyByUserId($utilisateurId, $onlySubscribed);
            return View::create($companies, Response::HTTP_OK);
        }
        $companies = $this->companyService->getCompanyByUserId($utilisateurId);
        return View::create($companies, Response::HTTP_OK);
    }


    //Méthode denvoie de mail
    private function sendMail($receiver, $subject, $bodyMessage)
    {
        $dsn = $this->getParameter('url');
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from('no-reply@sakabay.com')
            ->to($receiver)
            ->addTo('andreasadelson@gmail.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text($bodyMessage)
            // ->html('<p>See Twig integration for better HTML integration!</p>')
        ;

        $mailer->send($email);
    }

    private function recastName(string $urlName): string
    {
        $urlName = str_replace(' ', '-', $urlName);
        $urlName = str_replace('\'', '', $urlName);
        $unwanted_array = array(
            'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y'
        );

        return $urlName = strtolower(strtr($urlName, $unwanted_array));
    }
}
