<?php

namespace App\Application\Service;

use App\Domain\Model\Category;
use App\Infrastructure\Repository\CategoryRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;

class CategoryService
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * CategoryRestController constructor.
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /// Afficher un Category
    public function getCategory(int $categoryId): ?Category
    {
        return $this->categoryRepository->find($categoryId);
    }

    public function getAllCategorys(): ?array
    {
        return $this->categoryRepository->findAll();
    }

    public function deleteCategory(int $categoryId): void
    {
        $category = $this->categoryRepository->find($categoryId);
        if (!$category) {
            throw new EntityNotFoundException('Category with id ' . $categoryId . ' does not exist!');
        }
        $this->categoryRepository->delete($category);
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
        $perPage = PHP_INT_MAX ? PHP_INT_MAX : 10
    ) {
        return $this->categoryRepository
            ->getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage);
    }
}
