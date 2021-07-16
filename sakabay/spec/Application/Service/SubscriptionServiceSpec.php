<?php

namespace spec\App\Application\Service;

use App\Application\Service\SubscriptionService;
use App\Domain\Model\Subscription;
use App\Infrastructure\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SubscriptionServiceSpec extends ObjectBehavior
{
    public function let(
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->beConstructedWith($subscriptionRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(SubscriptionService::class);
    }

    public function it_should_get_subscription($subscriptionRepository)
    {
        $subscriptionRepository->find(0)->willReturn(null);
        $this->getSubscription(0)->shouldReturn(null);
        $subscriptionRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_subscription($subscriptionRepository)
    {
        $subscriptionRepository->findBy([], ['price' => 'ASC'])->willReturn([]);
        $this->getAllSubscriptions()->shouldReturn([]);
        $subscriptionRepository->findBy([], ['price' => 'ASC'])->shouldBeCalled();
    }

    /**
     * deleteSubscription(int $subscriptionId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($subscriptionRepository)
    {
        $subscriptionRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Subscription with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteSubscription', [0]);
    }

    public function it_should_delete_subscription($subscriptionRepository)
    {
        $subscription = new Subscription();
        $subscriptionRepository->find(0)->willReturn($subscription);
        $subscriptionRepository->delete(Argument::any());
        $this->deleteSubscription(0);
        $subscriptionRepository->find(0)->shouldBeCalled();
        $subscriptionRepository->delete($subscription)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($subscriptionRepository)
    {
        $subscriptionRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $subscriptionRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }

    public function it_should_get_subscription_by_name($subscriptionRepository)
    {
        $subscriptionRepository->findOneBy(['name' => 'test'])->willReturn(null);
        $this->getSubscriptionByName('test')->shouldReturn(null);
        $subscriptionRepository->findOneBy(['name' => 'test'])->shouldBeCalled();
    }

    public function it_should_get_subscription_by_code($subscriptionRepository)
    {
        $subscriptionRepository->findOneBy(['code' => 'test'])->willReturn(null);
        $this->getSubscriptionByCode('test')->shouldReturn(null);
        $subscriptionRepository->findOneBy(['code' => 'test'])->shouldBeCalled();
    }
}
