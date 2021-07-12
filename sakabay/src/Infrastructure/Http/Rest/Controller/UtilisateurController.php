<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\EditUtilisateurType;
use App\Application\Form\Type\EditAccountType;
use App\Application\Service\UtilisateurService;
use App\Application\Service\FileUploader;
use App\Infrastructure\Factory\NotificationFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Context\Context;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UtilisateurController extends AbstractFOSRestController
{
    private $entityManager;
    private $utilisateurService;
    private $notificationFactory;
    private $translator;

    /**
     * UtilisateurRestController constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UtilisateurService $utilisateurService,
        TranslatorInterface $translator,
        NotificationFactory $notificationFactory
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->utilisateurService = $utilisateurService;
        $this->notificationFactory = $notificationFactory;
    }

    /**
     * @Rest\View(serializerGroups={"api_utilisateurs"})
     * @Rest\Get("/admin/utilisateurs")
     *
     * @QueryParam(name="filterFields",
     *             default="email,username,firstName,lastName",
     *             description="Liste des champs sur lesquels le filtre s'appuie"
     * )
     * @QueryParam(name="filter",
     *             default="",
     *             description="Filtre"
     * )
     * @QueryParam(name="sortBy",
     *             default="lastName",
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
     *
     * - - - - - - - - - - - - Autocomplete - - - - - - - - - - - -
     *
     * @QueryParam(name="autocomplete",
     *             default="",
     *             description="Nom &| Prénom &| Email de l'utilisateur"
     * )
     * @return View
     */
    public function getUtilisateurs(ParamFetcher $paramFetcher): View
    {
        if ($paramFetcher->get('autocomplete')) {
            $utilisateurs = $this->utilisateurService->findUsersForAutocomplete($paramFetcher->all());

            return View::create($utilisateurs, Response::HTTP_OK);
        } else {
            $filterFields = $paramFetcher->get('filterFields');
            $filter = $paramFetcher->get('filter');
            $sortBy = $paramFetcher->get('sortBy');
            $sortDesc = $paramFetcher->get('sortDesc');
            $currentPage = $paramFetcher->get('currentPage');
            $perPage = $paramFetcher->get('perPage');

            $pager = $this->utilisateurService
                ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
            $utilisateurs = $pager->getCurrentPageResults();
            $nbResults = $pager->getNbResults();
            $datas = iterator_to_array($utilisateurs);
            $view = $this->view($datas, Response::HTTP_OK);
            $view->setHeader('X-Total-Count', $nbResults);

            return $view;
        }
    }

    /**
     * @Rest\Get("/admin/utilisateurs/{utilisateurId}")
     * @QueryParam(name="serial_group",
     *             nullable=false,
     *             default="api_utilisateurs",
     *             description="Un groupe de sérialisation parmis les groupes autorisés"
     * )
     *
     * @return View
     */
    public function getUtilisateur(int $utilisateurId, ParamFetcher $paramFetcher): View
    {
        $utilisateur = $this->utilisateurService->getUtilisateur($utilisateurId);
        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur with id ' . $utilisateurId . ' does not exist!');
        }

        $authorizedSerialGroups = [
            'api_utilisateurs',
            'api_dashboard_utilisateur'
        ];
        $querySerial = $paramFetcher->get('serial_group');
        if (in_array($querySerial, $authorizedSerialGroups)) {
            $serializerGroups = [$querySerial];
        } else {
            $serializerGroups = ['api_utilisateurs'];
        }
        $context = new Context();
        $context->setGroups($serializerGroups);

        return View::create($utilisateur, Response::HTTP_OK)->setContext($context);
    }

    /**
     * @Rest\View()
     * @Rest\Post("/admin/utilisateurs/{utilisateurId}")
     *
     * @return View
     */
    public function editUtilisateur(int $utilisateurId, Request $request)
    {
        $utilisateur = $this->utilisateurService->getUtilisateur($utilisateurId);
        dump($this->isCsrfTokenValid('utilisateur', $request->request->get('_token')));
        if (!$this->isCsrfTokenValid('utilisateur', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$utilisateur) {
            throw new NotFoundHttpException('Utilisateur with id ' . $utilisateurId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(EditUtilisateurType::class, $utilisateur, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('user_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Post("/utilisateur/edit/{utilisateurId}")
     *
     * @return View
     */
    public function editAccount(int $utilisateurId, Request $request, string $uploadDir, FileUploader $uploader, UserPasswordEncoderInterface $passwordEncoder)
    {
        $utilisateur = $this->utilisateurService->getUtilisateur($utilisateurId);
        if (!$this->isCsrfTokenValid('utilisateur', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$utilisateur) {
            throw new EntityNotFoundException('Utilisateur with id ' . $utilisateurId . ' does not exist!');
        }
        $file = $request->files->get('file');

        if (!empty($file)) {
            $newFileName = $uploader->setFileName($file);
            $uploader->upload($uploadDir, $file, $newFileName);
            $request->request->set('imageProfil', $newFileName);
            $oldImage = $utilisateur->getImageProfil();
            if (!empty($oldImage)) {
                if (file_exists($oldImage)) {
                    $uploader->deleteOldFile($uploadDir, $oldImage);
                }
            }
        }
        $formOptions = [
            'translator' => $this->translator,
        ];

        $form = $this->createForm(EditAccountType::class, $utilisateur, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        if ($form->isSubmitted() && !empty($form->get('plainPassword')->getData())) {
            $utilisateur->setPassword(
                $passwordEncoder->encodePassword(
                    $utilisateur,
                    $form->get('plainPassword')->getData()
                )
            );
            $this->notificationFactory->updatePassword([$utilisateur]);
            $email = $utilisateur->getEmail();
            $subject = $this->translator->trans('email_update_password_subject');
            $bodyMessage = $this->translator->trans('email_update_password_body');
            $this->sendMail($email, $subject, $bodyMessage);
        }
        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('dashboard');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    //Méthode denvoie de mail
    public function sendMail($receiver, $subject, $bodyMessage)
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

    /**
     * @Rest\View()
     * @Rest\Delete("/admin/utilisateurs/{utilisateurId}")
     *
     * @return View
     */
    public function deleteUtilisateurs(int $utilisateurId): View
    {
        try {
            $utilisateur = $this->utilisateurService->deleteUtilisateur($utilisateurId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('user_index');

        return View::create($utilisateur, Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }
}
