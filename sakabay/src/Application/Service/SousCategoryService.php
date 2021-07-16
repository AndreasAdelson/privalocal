<?php

namespace App\Application\Service;

use App\Domain\Model\SousCategory;
use App\Infrastructure\Repository\SousCategoryRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SousCategoryService
{
    /**
     * @var SousCategoryRepositoryInterface
     */
    private $sousCategoryRepository;

    /**
     * SousCategoryRestController constructor.
     */
    public function __construct(SousCategoryRepositoryInterface $sousCategoryRepository)
    {
        $this->sousCategoryRepository = $sousCategoryRepository;
    }

    public function getSousCategory(int $sousCategoryId): ?SousCategory
    {
        return $this->sousCategoryRepository->find($sousCategoryId);
    }

    public function getAllSousCategorys(): ?array
    {
        return $this->sousCategoryRepository->findAll();
    }

    public function deleteSousCategory(int $sousCategoryId): void
    {
        $sousCategory = $this->sousCategoryRepository->find($sousCategoryId);
        if (!$sousCategory) {
            throw new EntityNotFoundException('SousCategory with id ' . $sousCategoryId . ' does not exist!');
        }
        $this->sousCategoryRepository->delete($sousCategory);
    }

    /**
     * Retourne une page, potentiellement triée et filtrée.
     *
     *
     * @param string $sortBy
     * @param bool   $descending
     * @param string $filterFields
     * @param string $filterText
     * @param int    $currentPage
     * @param int    $perPage
     *
     * @return Pagerfanta
     */
    public function getPaginatedList(
        $sortBy = 'id',
        $descending = false,
        $filterFields = '',
        $filterText = '',
        $currentPage = 1,
        $perPage = PHP_INT_MAX ? PHP_INT_MAX : 10,
        $category = ''
    ) {
        return $this->sousCategoryRepository
            ->getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage, $category);
    }
}
