<?php

namespace spec\App\Application\Service;

use App\Application\Service\AdvantageService;
use App\Domain\Model\Advantage;
use App\Infrastructure\Repository\AdvantageRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AdvantageServiceSpec extends ObjectBehavior
{
    public function let(
        AdvantageRepository $advantageRepository
    ) {
        $this->beConstructedWith($advantageRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AdvantageService::class);
    }

    public function it_should_get_advantage($advantageRepository)
    {
        $advantageRepository->find(0)->willReturn(null);
        $this->getAdvantage(0)->shouldReturn(null);
        $advantageRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_advantage($advantageRepository)
    {
        $advantageRepository->findAll()->willReturn([]);
        $this->getAllAdvantages()->shouldReturn([]);
        $advantageRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteAdvantage(int $advantageId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($advantageRepository)
    {
        $advantageRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Advantage with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteAdvantage', [0]);
    }

    public function it_should_delete_advantage($advantageRepository)
    {
        $advantage = new Advantage();
        $advantageRepository->find(0)->willReturn($advantage);
        $advantageRepository->delete(Argument::any());
        $this->deleteAdvantage(0);
        $advantageRepository->find(0)->shouldBeCalled();
        $advantageRepository->delete($advantage)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($advantageRepository)
    {
        $advantageRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $advantageRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }
}
