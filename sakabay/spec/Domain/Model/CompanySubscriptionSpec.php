<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\CompanySubscription;
use App\Domain\Model\Subscription;
use App\Domain\Model\Company;
use App\Domain\Model\SubscriptionStatus;
use PhpSpec\ObjectBehavior;

class CompanySubscriptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(CompanySubscription::class);
        $this->getId()->shouldReturn(null);
    }

    public function it_should_save_a_dt_debut()
    {
        $this->setDtDebut(new \DateTime('2018-01-03 00:05:30'));
        $this->getDtDebut()->format('d-m-Y')->shouldReturn('03-01-2018');
    }

    public function it_should_save_a_dt_fin()
    {
        $this->setDtFin(new \DateTime('2018-01-03 00:05:30'));
        $this->getDtFin()->format('d-m-Y')->shouldReturn('03-01-2018');
    }

    public function it_should_save_a_subscription()
    {
        $subscription = new Subscription();
        $this->setSubscription($subscription);
        $this->getSubscription()->shouldReturn($subscription);
    }

    public function it_should_save_a_company()
    {
        $company = new Company();
        $this->setCompany($company);
        $this->getCompany()->shouldReturn($company);
    }

    public function it_should_save_a_stripe_id()
    {
        $this->setStripeId('test stripe id');
        $this->getStripeId()->shouldReturn('test stripe id');
    }

    public function it_should_save_a_susbcription_status()
    {
        $subscriptionStatus = new SubscriptionStatus();
        $this->setSubscriptionStatus($subscriptionStatus);
        $this->getSubscriptionStatus()->shouldReturn($subscriptionStatus);
    }
}
