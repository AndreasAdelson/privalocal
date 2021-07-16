<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\SubscriptionStatus;
use App\Domain\Model\Besoin;
use App\Domain\Model\Company;
use App\Domain\Model\CompanySubscription;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SubscriptionStatusSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(SubscriptionStatus::class);
        $this->getId()->shouldReturn(null);
    }

    public function it_should_save_a_name()
    {
        $this->setName('test name');
        $this->getName()->shouldReturn('test name');
    }

    public function it_should_save_a_code()
    {
        $this->setCode('test code');
        $this->getCode()->shouldReturn('test code');
    }

    public function it_should_add_and_remove_company_subscription()
    {
        $companySubscription = new CompanySubscription();
        $this->addCompanySubscription($companySubscription);
        $this->getCompanySubscriptions()->shouldContain($companySubscription);
        $this->getCompanySubscriptions()->shouldHaveCount(1);
        $this->removeCompanySubscription($companySubscription);
        $this->getCompanySubscriptions()->shouldHaveCount(0);
    }
}
