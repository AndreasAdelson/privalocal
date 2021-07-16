<?php

namespace spec\App\Application\Service;

use App\Application\Service\BesoinStatutService;
use App\Domain\Model\BesoinStatut;
use App\Infrastructure\Repository\BesoinStatutRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BesoinStatutServiceSpec extends ObjectBehavior
{
    public function let(
        BesoinStatutRepository $besoinStatutRepository
    ) {
        $this->beConstructedWith($besoinStatutRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(BesoinStatutService::class);
    }

    public function it_should_get_besoinStatut($besoinStatutRepository)
    {
        $besoinStatutRepository->find(0)->willReturn(null);
        $this->getBesoinStatut(0)->shouldReturn(null);
        $besoinStatutRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_besoinStatut($besoinStatutRepository)
    {
        $besoinStatutRepository->findAll()->willReturn([]);
        $this->getAllBesoinStatuts()->shouldReturn([]);
        $besoinStatutRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteBesoinStatut(int $besoinStatutId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($besoinStatutRepository)
    {
        $besoinStatutRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('BesoinStatut with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteBesoinStatut', [0]);
    }

    public function it_should_delete_besoinStatut($besoinStatutRepository)
    {
        $besoinStatut = new BesoinStatut();
        $besoinStatutRepository->find(0)->willReturn($besoinStatut);
        $besoinStatutRepository->delete(Argument::any());
        $this->deleteBesoinStatut(0);
        $besoinStatutRepository->find(0)->shouldBeCalled();
        $besoinStatutRepository->delete($besoinStatut)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($besoinStatutRepository)
    {
        $besoinStatutRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $besoinStatutRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }

    public function it_should_get_besoin_statut_by_code($besoinStatutRepository)
    {
        $besoinStatutRepository->findOneBy(['code' => 'VAL'])->willReturn([0]);
        $this->getBesoinStatutByCode('VAL')->shouldReturn([0]);
        $besoinStatutRepository->findOneBy(['code' => 'VAL'])->shouldBeCalled();
    }

    public function it_should_get_besoin_statut_without_pub($besoinStatutRepository)
    {
        $besoinStatutRepository->getBesoinStatutsWithoutPUB()->willReturn([0]);
        $this->getBesoinStatutsWithoutPUB()->shouldReturn([0]);
        $besoinStatutRepository->getBesoinStatutsWithoutPUB()->shouldBeCalled();
    }
}
