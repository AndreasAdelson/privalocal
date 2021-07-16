<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\AnswerSendQuoteType;
use App\Application\Form\Type\AnswerType;
use App\Application\Service\AnswerService;
use App\Application\Service\FileUploader;
use App\Domain\Model\Answer;
use App\Infrastructure\Factory\NotificationFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AnswerController extends AbstractFOSRestController
{
    private $entityManager;
    private $answerService;
    private $translator;

    /**
     * AnswerRestController constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AnswerService $answerService,
        TranslatorInterface $translator,
        NotificationFactory $notificationFactory
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->answerService = $answerService;
        $this->notificationFactory = $notificationFactory;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/answer")
     * @param Request $request
     *
     * @return View
     */
    public function createAnswer(Request $request)
    {
        $answer = new Answer();

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(AnswerType::class, $answer, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }

        //Send notification to user
        $authorOpportunity = $answer->getBesoin()->getAuthor();
        $ressourceLocation = $this->generateUrl('service_list');
        $this->notificationFactory->createAnswerNotification([$authorOpportunity], $ressourceLocation, $answer);

        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        return View::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\View(serializerGroups={"api_answers"})
     * @Rest\Get("admin/answers")
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
    public function getAnswers(ParamFetcher $paramFetcher): View
    {
        $filterFields = $paramFetcher->get('filterFields');
        $filter = $paramFetcher->get('filter');
        $sortBy = $paramFetcher->get('sortBy');
        $sortDesc = $paramFetcher->get('sortDesc');
        $currentPage = $paramFetcher->get('currentPage');
        $perPage = $paramFetcher->get('perPage');

        $pager = $this->answerService
            ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
        $answers = $pager->getCurrentPageResults();
        $nbResults = $pager->getNbResults();
        $datas = iterator_to_array($answers);
        $view = $this->view($datas, Response::HTTP_OK);
        $view->setHeader('X-Total-Count', $nbResults);

        return $view;
    }

    /**
     * @Rest\View(serializerGroups={"api_answer"})
     * @Rest\Get("admin/answers/{answerId}")
     *
     * @return View
     */
    public function getAnswer(int $answerId): View
    {
        $answer = $this->answerService->getAnswer($answerId);

        return View::create($answer, Response::HTTP_OK);
    }


    /**
     * @Rest\View()
     * @Rest\Post("admin/answers/edit/{answerId}")
     * @return View
     */
    public function editAnswer(int $answerId, Request $request)
    {
        $answer = $this->answerService->getAnswer($answerId);

        if (!$answer) {
            throw new EntityNotFoundException('Answer with id ' . $answerId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(AnswerType::class, $answer, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('answer_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/answers/{answerId}")
     *
     * @return View
     */
    public function deleteAnswers(int $answerId): View
    {
        try {
            $answer = $this->answerService->deleteAnswer($answerId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('answer_index');

        return View::create($answer, Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Post("answers/quote/{answerId}")
     * @return View
     */
    public function requestQuote(int $answerId)
    {
        $answer = $this->answerService->getAnswer($answerId);

        if (!$answer) {
            throw new EntityNotFoundException('Answer with id ' . $answerId . ' does not exist!');
        }

        //Notification à l'entreprise
        $companyOwner = $answer->getCompany()->getUtilisateur();
        $ressourceLocation = $this->generateUrl('opportunity_list', ['page' => 'quote']);
        $this->notificationFactory->requestQuoteNotification([$companyOwner], $ressourceLocation, $answer);

        //Changement du champ requestQuote à vrai
        $answer->setRequestQuote(true);
        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        return View::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\View()
     * @Rest\Post("answers/quote/{answerId}/send/{utilisateurId}")
     * @return View
     */
    public function sendQuote(int $answerId, int $utilisateurId, Request $request, string $uploadDir, FileUploader $uploader)
    {
        $answer = $this->answerService->getAnswer($answerId);

        if (!$answer) {
            throw new EntityNotFoundException('Answer with id ' . $answerId . ' does not exist!');
        }

        $besoinStatut = $answer->getBesoin()->getBesoinStatut()->getCode();
        if ($besoinStatut !== 'PUB') {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_send_quote')),
            ]);
        }
        $companyOwnerId = $answer->getCompany()->getUtilisateur()->getId();
        $request->request->remove('utilisateur');
        if ($companyOwnerId != $utilisateurId || $answer->getCompany()->getCompanystatut()->getCode() != 'VAL') {
            throw $this->createAccessDeniedException();
        }
        $companyUrlName = $answer->getCompany()->getUrlName();
        $uploadedFile = $request->files->get('file');
        $submittedData = $request->request->all();
        if (!empty($uploadedFile)) {
            try {
                $newFileName = $uploader->setFileName($uploadedFile);
                $submittedData['quote'] = $newFileName;
                $submittedData['file'] = $uploadedFile;
            } catch (InvalidArgumentException $e) {
                return View::create([], Response::HTTP_BAD_REQUEST, [
                    'X-Message' => rawurlencode($this->translator->trans('error_mime_types_message')),
                ]);
            }
        } else {
            $submittedData['file'] = null;
        }
        $formOptions = [
            'translator' => $this->translator,
        ];

        $form = $this->createForm(AnswerSendQuoteType::class, $answer, $formOptions);
        $form->submit($submittedData);
        if ($form->isValid()) {
            $file = $form['file']->getData();
            if ($file) {
                try {
                    $newFileName = $uploader->setFileName($file);
                    $uploadDirectory = $uploadDir . '/' . $companyUrlName;
                    $uploader->upload($uploadDirectory, $file, $newFileName);
                    //Send email with document and message
                    $senderEmail = $answer->getCompany()->getUtilisateur()->getEmail();
                    $receiverEmail = $answer->getBesoin()->getAuthor()->getEmail();
                    $subject = $this->translator->trans('email_send_quote_subject');
                    $bodyMessage = $request->request->get('messageEmail');
                    $this->sendMail($senderEmail, $receiverEmail, $subject, $bodyMessage, $uploadDirectory . '/' . $newFileName);
                    $this->entityManager->persist($answer);
                    $this->entityManager->flush();
                } catch (FileNotFoundException | IOException $e) {
                    return View::create([], Response::HTTP_BAD_REQUEST, [
                        'X-Message' => 'File not found exception',
                    ]);
                }
                $ressourceLocation = $this->generateUrl('opportunity_list', ['page' => 'quote']);
                return View::create([], Response::HTTP_OK, ['Location' => $ressourceLocation]);
            }
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => 'Une erreur s\'est produite lors du chargement du fichier.',
            ]);
        }
        return View::create($form, Response::HTTP_BAD_REQUEST);
    }

    //Méthode denvoie de mail
    public function sendMail($deliverer, $receiver, $subject, $bodyMessage, $attachementDir)
    {
        $dsn = $this->getParameter('url');
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from($deliverer)
            ->to($receiver)
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text($bodyMessage)
            ->attachFromPath($attachementDir)
            // ->html('<p>See Twig integration for better HTML integration!</p>')
        ;

        $mailer->send($email);
    }
}
