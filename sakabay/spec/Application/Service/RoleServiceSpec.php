<?php

namespace spec\App\Application\Service;

use App\Application\Service\RoleService;
use App\Domain\Model\Role;
use App\Infrastructure\Repository\RoleRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RoleServiceSpec extends ObjectBehavior
{
    public function let(
        RoleRepository $roleRepository
    ) {
        $this->beConstructedWith($roleRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RoleService::class);
    }

    public function it_should_get_role($roleRepository)
    {
        $roleRepository->find(0)->willReturn(null);
        $this->getRole(0)->shouldReturn(null);
        $roleRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_role($roleRepository)
    {
        $roleRepository->findAll()->willReturn([]);
        $this->getAllRoles()->shouldReturn([]);
        $roleRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteRole(int $roleId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($roleRepository)
    {
        $roleRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Role with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteRole', [0]);
    }

    public function it_should_delete_role($roleRepository)
    {
        $role = new Role();
        $roleRepository->find(0)->willReturn($role);
        $roleRepository->delete(Argument::any());
        $this->deleteRole(0);
        $roleRepository->find(0)->shouldBeCalled();
        $roleRepository->delete($role)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($roleRepository)
    {
        $roleRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $roleRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }
}
