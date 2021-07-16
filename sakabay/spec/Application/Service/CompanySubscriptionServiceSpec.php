<?php

namespace spec\App\Application\Service;

use App\Application\Service\CompanySubscriptionService;
use App\Domain\Model\CompanySubscription;
use App\Domain\Model\SubscriptionStatus;
use App\Infrastructure\Repository\CompanySubscriptionRepository;
use DateTime;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CompanySubscriptionServiceSpec extends ObjectBehavior
{
    public function let(
        CompanySubscriptionRepository $companySubscriptionRepository
    ) {
        $this->beConstructedWith($companySubscriptionRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CompanySubscriptionService::class);
    }

    public function it_should_get_company_subscription($companySubscriptionRepository)
    {
        $companySubscriptionRepository->find(0)->willReturn(null);
        $this->getCompanySubscription(0)->shouldReturn(null);
        $companySubscriptionRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_company_subscription($companySubscriptionRepository)
    {
        $companySubscriptionRepository->findAll()->willReturn([]);
        $this->getAllCompanySubscriptions()->shouldReturn([]);
        $companySubscriptionRepository->findAll()->shouldBeCalled();
    }

    public function it_should_get_active_subscription($companySubscriptionRepository)
    {
        $companySubscriptionRepository->getActiveSubscription(1, 2, 3)->willReturn([]);
        $this->getActiveSubscription(1, 2, 3)->shouldReturn([]);
        $companySubscriptionRepository->getActiveSubscription(1, 2, 3)->shouldBeCalled();
    }

    public function it_should_find_company_subscription_by_stripe_id($companySubscriptionRepository)
    {
        $companySubscription = new CompanySubscription();
        $companySubscriptionRepository->findOneBy(['stripeId' => 0])->willReturn($companySubscription);
        $this->findCompanySubscriptionByStripeId(0)->shouldReturn($companySubscription);
        $companySubscriptionRepository->findOneBy(['stripeId' => 0])->shouldBeCalled();
    }

    public function it_should_throw_error_when_trying_to_find_non_existant_company_subscription_by_strie_id($companySubscriptionRepository)
    {
        $companySubscriptionRepository->findOneBy(['stripeId' => 0])->willReturn(null);

        $expectedException = new Exception('Somehow we have no companySubscription id 0');
        $this->shouldThrow($expectedException)->during('findCompanySubscriptionByStripeId', [0]);
    }

    public function it_should_end_company_subscription_by_stripe_id($companySubscriptionRepository)
    {
        $companySubscription = new CompanySubscription();
        $companySubscriptionRepository->findOneBy(['stripeId' => 0])->willReturn($companySubscription);
        $this->endCompanySubscriptionByStripeId(0, new DateTime('2010-01-01 00:00:00'));
        $companySubscriptionRepository->save($companySubscription)->shouldBeCalled();
    }

    public function it_should_set_status_company_susbcription_by_stripe_id($companySubscriptionRepository)
    {
        $companySubscription = new CompanySubscription();
        $subscriptionStatus = new SubscriptionStatus();
        $companySubscriptionRepository->findOneBy(['stripeId' => 0])->willReturn($companySubscription);
        $this->setStatusCompanySubscriptionByStripeId(0, $subscriptionStatus);
        $companySubscriptionRepository->save($companySubscription)->shouldBeCalled();
    }
}
