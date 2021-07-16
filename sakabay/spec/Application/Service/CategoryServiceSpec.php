<?php

namespace spec\App\Application\Service;

use App\Application\Service\CategoryService;
use App\Domain\Model\Category;
use App\Infrastructure\Repository\CategoryRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CategoryServiceSpec extends ObjectBehavior
{
    public function let(
        CategoryRepository $categoryRepository
    ) {
        $this->beConstructedWith($categoryRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CategoryService::class);
    }

    public function it_should_get_category($categoryRepository)
    {
        $categoryRepository->find(0)->willReturn(null);
        $this->getCategory(0)->shouldReturn(null);
        $categoryRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_category($categoryRepository)
    {
        $categoryRepository->findAll()->willReturn([]);
        $this->getAllCategorys()->shouldReturn([]);
        $categoryRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteCategory(int $categoryId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($categoryRepository)
    {
        $categoryRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Category with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteCategory', [0]);
    }

    public function it_should_delete_category($categoryRepository)
    {
        $category = new Category();
        $categoryRepository->find(0)->willReturn($category);
        $categoryRepository->delete(Argument::any());
        $this->deleteCategory(0);
        $categoryRepository->find(0)->shouldBeCalled();
        $categoryRepository->delete($category)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($categoryRepository)
    {
        $categoryRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $categoryRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }
}
