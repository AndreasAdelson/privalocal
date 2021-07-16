<?php

namespace spec\App\Application\Service;

use App\Application\Service\CompanyStatutService;
use App\Domain\Model\CompanyStatut;
use App\Infrastructure\Repository\CompanyStatutRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CompanyStatutServiceSpec extends ObjectBehavior
{
    public function let(
        CompanyStatutRepository $companyStatutRepository
    ) {
        $this->beConstructedWith($companyStatutRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CompanyStatutService::class);
    }

    public function it_should_get_company_statut($companyStatutRepository)
    {
        $companyStatutRepository->find(0)->willReturn(null);
        $this->getCompanyStatut(0)->shouldReturn(null);
        $companyStatutRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_company_statut($companyStatutRepository)
    {
        $companyStatutRepository->findAll()->willReturn([]);
        $this->getAllCompanyStatuts()->shouldReturn([]);
        $companyStatutRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteCompanyStatut(int $companyStatutId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($companyStatutRepository)
    {
        $companyStatutRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('CompanyStatut with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteCompanyStatut', [0]);
    }

    public function it_should_delete_company_statut($companyStatutRepository)
    {
        $companyStatut = new CompanyStatut();
        $companyStatutRepository->find(0)->willReturn($companyStatut);
        $companyStatutRepository->delete(Argument::any());
        $this->deleteCompanyStatut(0);
        $companyStatutRepository->find(0)->shouldBeCalled();
        $companyStatutRepository->delete($companyStatut)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($companyStatutRepository)
    {
        $companyStatutRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $companyStatutRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }

    public function it_should_get_company_statut_by_code($companyStatutRepository)
    {
        $companyStatutRepository->findOneBy(['code' => 'test'])->willReturn(null);
        $this->getCompanyStatutByCode('test')->shouldReturn(null);
        $companyStatutRepository->findOneBy(['code' => 'test'])->shouldBeCalled();
    }
}
