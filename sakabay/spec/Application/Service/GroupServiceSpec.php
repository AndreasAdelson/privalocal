<?php

namespace spec\App\Application\Service;

use App\Application\Service\GroupService;
use App\Domain\Model\Group;
use App\Infrastructure\Repository\GroupRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GroupServiceSpec extends ObjectBehavior
{
    public function let(
        GroupRepository $groupRepository
    ) {
        $this->beConstructedWith($groupRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(GroupService::class);
    }

    public function it_should_get_group($groupRepository)
    {
        $groupRepository->find(0)->willReturn(null);
        $this->getGroup(0)->shouldReturn(null);
        $groupRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_group($groupRepository)
    {
        $groupRepository->findAll()->willReturn([]);
        $this->getAllGroups()->shouldReturn([]);
        $groupRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteGroup(int $groupId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($groupRepository)
    {
        $groupRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Group with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteGroup', [0]);
    }

    public function it_should_delete_group($groupRepository)
    {
        $group = new Group();
        $groupRepository->find(0)->willReturn($group);
        $groupRepository->delete(Argument::any());
        $this->deleteGroup(0);
        $groupRepository->find(0)->shouldBeCalled();
        $groupRepository->delete($group)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($groupRepository)
    {
        $groupRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $groupRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }
}
