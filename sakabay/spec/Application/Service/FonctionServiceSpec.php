<?php

namespace spec\App\Application\Service;

use App\Application\Service\FonctionService;
use App\Domain\Model\Fonction;
use App\Infrastructure\Repository\FonctionRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FonctionServiceSpec extends ObjectBehavior
{
    public function let(
        FonctionRepository $fonctionRepository
    ) {
        $this->beConstructedWith($fonctionRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(FonctionService::class);
    }

    public function it_should_get_fonction($fonctionRepository)
    {
        $fonctionRepository->find(0)->willReturn(null);
        $this->getFonction(0)->shouldReturn(null);
        $fonctionRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_fonction($fonctionRepository)
    {
        $fonctionRepository->findAll()->willReturn([]);
        $this->getAllFonctions()->shouldReturn([]);
        $fonctionRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteFonction(int $fonctionId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($fonctionRepository)
    {
        $fonctionRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Fonction with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteFonction', [0]);
    }

    public function it_should_delete_fonction($fonctionRepository)
    {
        $fonction = new Fonction();
        $fonctionRepository->find(0)->willReturn($fonction);
        $fonctionRepository->delete(Argument::any());
        $this->deleteFonction(0);
        $fonctionRepository->find(0)->shouldBeCalled();
        $fonctionRepository->delete($fonction)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($fonctionRepository)
    {
        $fonctionRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $fonctionRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }
}
