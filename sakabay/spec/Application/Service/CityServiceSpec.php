<?php

namespace spec\App\Application\Service;

use App\Application\Service\CityService;
use App\Domain\Model\City;
use App\Infrastructure\Repository\CityRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CityServiceSpec extends ObjectBehavior
{
    public function let(
        CityRepository $cityRepository
    ) {
        $this->beConstructedWith($cityRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CityService::class);
    }

    public function it_should_get_city($cityRepository)
    {
        $cityRepository->find(0)->willReturn(null);
        $this->getCity(0)->shouldReturn(null);
        $cityRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_city($cityRepository)
    {
        $cityRepository->findAll()->willReturn([]);
        $this->getAllCitys()->shouldReturn([]);
        $cityRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteCity(int $cityId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($cityRepository)
    {
        $cityRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('City with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteCity', [0]);
    }

    public function it_should_delete_city($cityRepository)
    {
        $city = new City();
        $cityRepository->find(0)->willReturn($city);
        $cityRepository->delete(Argument::any());
        $this->deleteCity(0);
        $cityRepository->find(0)->shouldBeCalled();
        $cityRepository->delete($city)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($cityRepository)
    {
        $cityRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $cityRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }

    public function it_should_find_cities_for_autocomplete($cityRepository)
    {
        $cityRepository->findCitiesForAutocomplete([0])->willReturn([0]);
        $this->findCitiesForAutocomplete([0])->shouldReturn([0]);
        $cityRepository->findCitiesForAutocomplete([0])->shouldBeCalled();
    }
}
