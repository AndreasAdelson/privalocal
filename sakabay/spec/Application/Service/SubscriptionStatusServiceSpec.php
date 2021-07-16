<?php

namespace spec\App\Application\Service;

use App\Application\Service\SubscriptionStatusService;
use App\Domain\Model\SubscriptionStatus;
use App\Infrastructure\Repository\SubscriptionStatusRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SubscriptionStatusServiceSpec extends ObjectBehavior
{
    public function let(
        SubscriptionStatusRepository $subscriptionStatusRepository
    ) {
        $this->beConstructedWith($subscriptionStatusRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(SubscriptionStatusService::class);
    }

    public function it_should_get_subscription_status($subscriptionStatusRepository)
    {
        $subscriptionStatusRepository->find(0)->willReturn(null);
        $this->getSubscriptionStatus(0)->shouldReturn(null);
        $subscriptionStatusRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_subscription_status($subscriptionStatusRepository)
    {
        $subscriptionStatusRepository->findAll()->willReturn([]);
        $this->getAllSubscriptionStatus()->shouldReturn([]);
        $subscriptionStatusRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteSubscriptionStatus(int $subscriptionStatusId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($subscriptionStatusRepository)
    {
        $subscriptionStatusRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('SubscriptionStatus with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteSubscriptionStatus', [0]);
    }

    public function it_should_delete_subscription_status($subscriptionStatusRepository)
    {
        $subscriptionStatus = new SubscriptionStatus();
        $subscriptionStatusRepository->find(0)->willReturn($subscriptionStatus);
        $subscriptionStatusRepository->delete(Argument::any());
        $this->deleteSubscriptionStatus(0);
        $subscriptionStatusRepository->find(0)->shouldBeCalled();
        $subscriptionStatusRepository->delete($subscriptionStatus)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($subscriptionStatusRepository)
    {
        $subscriptionStatusRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $subscriptionStatusRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }
}
