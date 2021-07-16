<?php

namespace spec\App\Application\Service;

use App\Application\Service\BesoinService;
use App\Domain\Model\Besoin;
use App\Infrastructure\Repository\BesoinRepository;
use Doctrine\ORM\EntityNotFoundException;
use LogicException;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BesoinServiceSpec extends ObjectBehavior
{
    public function let(
        BesoinRepository $besoinRepository
    ) {
        $this->beConstructedWith($besoinRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(BesoinService::class);
    }

    public function it_should_get_besoin($besoinRepository)
    {
        $besoinRepository->find(0)->willReturn(null);
        $this->getBesoin(0)->shouldReturn(null);
        $besoinRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_besoin($besoinRepository)
    {
        $besoinRepository->findAll()->willReturn([]);
        $this->getAllBesoins()->shouldReturn([]);
        $besoinRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteBesoin(int $besoinId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($besoinRepository)
    {
        $besoinRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Besoin with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteBesoin', [0]);
    }

    public function it_should_delete_besoin($besoinRepository)
    {
        $besoin = new Besoin();
        $besoinRepository->find(0)->willReturn($besoin);
        $besoinRepository->delete(Argument::any());
        $this->deleteBesoin(0);
        $besoinRepository->find(0)->shouldBeCalled();
        $besoinRepository->delete($besoin)->shouldBeCalled();
    }

    public function it_should_get_besoin_answered_by_company($besoinRepository)
    {
        $besoinRepository->getBesoinAnsweredByCompany(0, 0)->willReturn(null);
        $this->getBesoinAnsweredByCompany(0, 0)->shouldReturn(null);
        $besoinRepository->getBesoinAnsweredByCompany(0, 0)->shouldBeCalled();
    }


    public function it_should_get_paginated_list($besoinRepository)
    {
        $besoinRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $besoinRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }


    public function it_should_get_paginated_besoin_by_user_id($besoinRepository)
    {
        $besoinRepository->getPaginatedBesoinByUserId(1, 2, 3, 'false')->willReturn([0]);
        $this->getPaginatedBesoinByUserId(1, 2, 3, 1, 10);
        $besoinRepository->getPaginatedBesoinByUserId(1, 2, 3, 'false')->shouldBeCalled();
    }


    public function it_should_get_count_besoin_by_user_id($besoinRepository)
    {
        $this->getCountBesoinByUserId(1, 2, 3, 4)->shouldReturn(null);
        $besoinRepository->getPaginatedBesoinByUserId(1, 2, 3, 4)->shouldBeCalled();
    }


    public function it_should_get_paginated_opportunity_list($besoinRepository)
    {
        $besoinRepository->getPaginatedOpportunityList(1, 2, 'false', 4)->willReturn([0]);
        $this->getPaginatedOpportunityList(1, 2, 4, 1, 10);
        $besoinRepository->getPaginatedOpportunityList(1, 2, 'false', 4)->shouldBeCalled();
    }


    public function it_should_get_count_opportunities($besoinRepository)
    {
        $this->getCountOpportunities(1, 2, 3, 4)->shouldReturn(null);
        $besoinRepository->getPaginatedOpportunityList(1, 2, 3, 4)->shouldBeCalled();
    }

    public function it_should_get_paginated_opportunity_with_request_quote_list($besoinRepository)
    {
        $besoinRepository->getPaginatedOpportunityWithRequestedQuoteList(1, false, 3)->willReturn([0]);
        $this->getPaginatedOpportunityWithRequestedQuoteList(1, 1, 10, 3);
        $besoinRepository->getPaginatedOpportunityWithRequestedQuoteList(1, false, 3)->shouldBeCalled();
    }


    public function it_should_get_count_opportunities_with_request_quote_list($besoinRepository)
    {
        $this->getCountOpportunitiesWithRequestedQuote(1, 2, 3)->shouldReturn(null);
        $besoinRepository->getPaginatedOpportunityWithRequestedQuoteList(1, 2, 3)->shouldBeCalled();
    }

    public function it_should_paginate_array()
    {
        $expectedException = new LogicException('$perPage must be greater than 0.');
        $this->shouldThrow($expectedException)->during('paginateArray', [[], 0, 1]);
        $expectedException = new LogicException('$currentPage must be greater than 0.');
        $this->shouldThrow($expectedException)->during('paginateArray', [[], 1, 0]);
    }
}
