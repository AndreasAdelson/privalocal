<?php

namespace spec\App\Application\Service;

use App\Application\Service\CompanyService;
use App\Domain\Model\Company;
use App\Domain\Model\CompanySubscription;
use App\Infrastructure\Repository\CommentRepository;
use App\Infrastructure\Repository\CompanyRepository;
use DateTime;
use Doctrine\ORM\EntityNotFoundException;
use LogicException;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CompanyServiceSpec extends ObjectBehavior
{
    public function let(
        CompanyRepository $companyRepository,
        CommentRepository $commentRepository
    ) {
        $this->beConstructedWith($companyRepository, $commentRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CompanyService::class);
    }

    public function it_should_get_company($companyRepository)
    {
        $companyRepository->find(0)->willReturn(null);
        $this->getCompany(0)->shouldReturn(null);
        $companyRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_company($companyRepository)
    {
        $companyRepository->findAll()->willReturn([]);
        $this->getAllCompanys()->shouldReturn([]);
        $companyRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteCompany(int $companyId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($companyRepository)
    {
        $companyRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Company with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteCompany', [0]);
    }

    public function it_should_delete_company($companyRepository)
    {
        $company = new Company();
        $companyRepository->find(0)->willReturn($company);
        $companyRepository->delete(Argument::any());
        $this->deleteCompany(0);
        $companyRepository->find(0)->shouldBeCalled();
        $companyRepository->delete($company)->shouldBeCalled();
    }

    public function it_should_get_company_by_url_name($companyRepository)
    {
        $companyRepository->findOneBy(['urlName' => 'test'])->willReturn(null);
        $this->getCompanyByUrlName('test')->shouldReturn(null);
        $companyRepository->findOneBy(['urlName' => 'test'])->shouldBeCalled();
    }


    public function it_should_get_paginated_list_admin($companyRepository)
    {
        $companyRepository->getPaginatedListAdmin(1, 2, 3, 4, 1, 10, 7)->willReturn([0]);
        $this->getPaginatedListAdmin(1, 2, 3, 4, 1, 10, 7)->shouldReturn([0]);
        $companyRepository->getPaginatedListAdmin(1, 2, 3, 4, 1, 10, 7)->shouldBeCalled();
    }

    public function it_should_paginate_array()
    {
        $expectedException = new LogicException('$perPage must be greater than 0.');
        $this->shouldThrow($expectedException)->during('paginateArray', [[], 0, 1]);
        $expectedException = new LogicException('$currentPage must be greater than 0.');
        $this->shouldThrow($expectedException)->during('paginateArray', [[], 1, 0]);
    }

    public function it_should_get_company_by_user_id($companyRepository)
    {
        $companyRepository->getCompanyByUserId(1, 2)->willReturn(null);
        $this->getCompanyByUserId(1, 2)->shouldReturn(null);
        $companyRepository->getCompanyByUserId(1, 2)->shouldBeCalled();
    }

    public function it_should_is_company_subscription_active($companyRepository)
    {
        $company = new Company();
        $companySubscription = new CompanySubscription();
        $companySubscription2 = new CompanySubscription();
        $companySubscription->setDtFin(new DateTime('2030-01-01 00:00:00'));
        $companySubscription2->setDtFin(new DateTime('2010-01-01 00:00:00'));
        $company->addCompanySubscription($companySubscription);
        $company->addCompanySubscription($companySubscription2);
        $companyRepository->getCompanyByUserId(1, 2)->willReturn(null);
        $this->isCompanySubscribtionActive($company)->shouldReturn(true);
        $companySubscription->setDtFin(new DateTime('2010-01-01 00:00:00'));
        $this->isCompanySubscribtionActive($company)->shouldReturn(false);
    }

    public function it_should_update_notation($companyRepository, $commentRepository)
    {
        $expectedException = new EntityNotFoundException('Company with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('updateNotation', [0]);
        $company = new Company();
        $companyRepository->find(0)->willReturn($company);
        $commentRepository->getAverageNotation(0)->willReturn([['note_avg' => 4.0]]);
        $this->updateNotation(0);
        $companyRepository->save($company)->shouldBeCalled();
    }

    public function it_should_get_paginated_list_user($companyRepository)
    {
        $companySusbcribed1 = new Company();
        $companySusbcribed2 = new Company();
        $companySusbcribed1->setName('a');
        $companySusbcribed2->setName('b');
        $companySusbcribed1->setId(1);
        $companySusbcribed2->setId(2);

        $companyNormal1 = new Company();
        $companyNormal2 = new Company();
        $companyNormal3 = new Company();
        $companyNormal4 = new Company();

        $companyNormal1->setName('c');
        $companyNormal2->setName('d');
        $companyNormal3->setName('e');
        $companyNormal4->setName('f');
        $companyNormal1->setId(3);
        $companyNormal2->setId(4);
        $companyNormal3->setId(5);
        $companyNormal4->setId(6);

        $companyNormal1->setNote(4);
        $companyNormal2->setNote(5);

        $companySubscription1 = new CompanySubscription();
        $companySubscription2 = new CompanySubscription();

        $companySubscription1->setDtFin(new DateTime('2030-01-01 00:00:00'));
        $companySubscription2->setDtFin(new DateTime('2030-01-01 00:00:00'));

        $companySusbcribed1->addCompanySubscription($companySubscription1);
        $companySusbcribed2->addCompanySubscription($companySubscription2);

        $companyNormal4->getNote();

        $companyRepository->getPaginatedListUser(1, 2, 3, 4, 7, 8, 9, 10)->willReturn([$companySusbcribed1, $companyNormal1, $companySusbcribed2, $companyNormal2, $companyNormal3, $companyNormal4]);
        $finalArray = [$companySusbcribed1, $companySusbcribed2, $companyNormal2, $companyNormal1, $companyNormal3, $companyNormal4];

        $this->getPaginatedListUser(1, 2, 3, 4, 1, 10, 7, 8, 9, 10);
        $companyRepository->getPaginatedListUser(1, 2, 3, 4, 7, 8, 9, 10)->shouldBeCalled();
        $this->paginateArray($finalArray, 10, 1)->shouldReturnAnInstanceOf(new Pagerfanta(new ArrayAdapter($finalArray)));
        $this->paginateArray($finalArray, 10, 1)->shouldHaveCount(6);
    }
}
