<?php

namespace App\Infrastructure\Http\Rest\Controller;

use App\Application\Form\Type\CityType;
use App\Application\Service\CityService;
use App\Domain\Model\City;
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

final class CityController extends AbstractFOSRestController
{
    private $entityManager;
    private $cityService;
    private $translator;

    /**
     * CityRestController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, CityService $cityService, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->cityService = $cityService;
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/cities")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     *
     * @return View
     */
    public function createCity(Request $request)
    {
        $city = new City();
        if (!$this->isCsrfTokenValid('city', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        $formOptions = ['translator' => $this->translator];
        $form = $this->createForm(CityType::class, $city, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($city);
        $this->entityManager->flush();

        $ressourceLocation = $this->generateUrl('city_index');

        return View::create([], Response::HTTP_CREATED, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View(serializerGroups={"api_cities"})
     * @Rest\Get("/admin/cities")
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
     *
     * - - - - - - - - - - - - Autocomplete - - - - - - - - - - - -
     *
     * @QueryParam(name="autocomplete",
     *             default="",
     *             description="Nom de la ville"
     * )
     * @return View
     */

    public function getCities(ParamFetcher $paramFetcher): View
    {
        if ($paramFetcher->get('autocomplete')) {
            $city = $this->cityService->findCitiesForAutocomplete($paramFetcher->all());

            return View::create($city, Response::HTTP_OK);
        } else {
            $filterFields = $paramFetcher->get('filterFields');
            $filter = $paramFetcher->get('filter');
            $sortBy = $paramFetcher->get('sortBy');
            $sortDesc = $paramFetcher->get('sortDesc');
            $currentPage = $paramFetcher->get('currentPage');
            $perPage = $paramFetcher->get('perPage');

            $pager = $this->cityService
                ->getPaginatedList($sortBy, 'true' === $sortDesc, $filterFields, $filter, $currentPage, $perPage);
            $cities = $pager->getCurrentPageResults();
            $nbResults = $pager->getNbResults();
            $datas = iterator_to_array($cities);
            $view = $this->view($datas, Response::HTTP_OK);
            $view->setHeader('X-Total-Count', $nbResults);

            return $view;
        }
    }
    /**
     * @Rest\View(serializerGroups={"api_cities"})
     * @Rest\Get("admin/cities/{cityId}")
     *
     * @return View
     */
    public function getCity(int $cityId): View
    {
        $city = $this->cityService->getCity($cityId);

        return View::create($city, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Post("admin/cities/{cityId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function editCity(int $cityId, Request $request)
    {
        $city = $this->cityService->getCity($cityId);
        if (!$this->isCsrfTokenValid('city', $request->request->get('_token'))) {
            return View::create([], Response::HTTP_BAD_REQUEST, [
                'X-Message' => rawurlencode($this->translator->trans('error_csrf_token')),
            ]);
        }
        if (!$city) {
            throw new EntityNotFoundException('City with id ' . $cityId . ' does not exist!');
        }

        $formOptions = [
            'translator' => $this->translator,
        ];
        $form = $this->createForm(CityType::class, $city, $formOptions);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
        $this->entityManager->persist($city);
        $this->entityManager->flush($city);

        $ressourceLocation = $this->generateUrl('city_index');
        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("admin/cities/{cityId}")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return View
     */
    public function deleteCities(int $cityId): View
    {
        try {
            $this->cityService->deleteCity($cityId);
        } catch (EntityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        $ressourceLocation = $this->generateUrl('city_index');

        return View::create([], Response::HTTP_NO_CONTENT, ['Location' => $ressourceLocation]);
    }
}
