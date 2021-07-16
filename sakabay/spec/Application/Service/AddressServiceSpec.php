<?php

namespace spec\App\Application\Service;

use App\Application\Service\AddressService;
use App\Domain\Model\Address;
use App\Infrastructure\Repository\AddressRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AddressServiceSpec extends ObjectBehavior
{
    public function let(
        AddressRepository $addressRepository
    ) {
        $this->beConstructedWith($addressRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AddressService::class);
    }

    public function it_should_get_address($addressRepository)
    {
        $addressRepository->find(0)->willReturn(null);
        $this->getAddress(0)->shouldReturn(null);
        $addressRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_address($addressRepository)
    {
        $addressRepository->findAll()->willReturn([]);
        $this->getAllAddresss()->shouldReturn([]);
        $addressRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteAddress(int $addressId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($addressRepository)
    {
        $addressRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Address with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteAddress', [0]);
    }

    public function it_should_delete_address($addressRepository)
    {
        $address = new Address();
        $addressRepository->find(0)->willReturn($address);
        $addressRepository->delete(Argument::any());
        $this->deleteAddress(0);
        $addressRepository->find(0)->shouldBeCalled();
        $addressRepository->delete($address)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($addressRepository)
    {
        $addressRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $addressRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }
}
