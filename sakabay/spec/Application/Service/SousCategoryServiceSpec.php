<?php

namespace spec\App\Application\Service;

use App\Application\Service\SousCategoryService;
use App\Domain\Model\SousCategory;
use App\Infrastructure\Repository\SousCategoryRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SousCategoryServiceSpec extends ObjectBehavior
{
    public function let(
        SousCategoryRepository $sousCategoryRepository
    ) {
        $this->beConstructedWith($sousCategoryRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(SousCategoryService::class);
    }

    public function it_should_get_sous_category($sousCategoryRepository)
    {
        $sousCategoryRepository->find(0)->willReturn(null);
        $this->getSousCategory(0)->shouldReturn(null);
        $sousCategoryRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_sous_category($sousCategoryRepository)
    {
        $sousCategoryRepository->findAll()->willReturn([]);
        $this->getAllSousCategorys()->shouldReturn([]);
        $sousCategoryRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteSousCategory(int $sousCategoryId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($sousCategoryRepository)
    {
        $sousCategoryRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('SousCategory with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteSousCategory', [0]);
    }

    public function it_should_delete_sous_category($sousCategoryRepository)
    {
        $sousCategory = new SousCategory();
        $sousCategoryRepository->find(0)->willReturn($sousCategory);
        $sousCategoryRepository->delete(Argument::any());
        $this->deleteSousCategory(0);
        $sousCategoryRepository->find(0)->shouldBeCalled();
        $sousCategoryRepository->delete($sousCategory)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($sousCategoryRepository)
    {
        $sousCategoryRepository->getPaginatedList(1, 2, 3, 4, 5, 6, 7)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6, 7)->shouldReturn([0]);
        $sousCategoryRepository->getPaginatedList(1, 2, 3, 4, 5, 6, 7)->shouldBeCalled();
    }
}
