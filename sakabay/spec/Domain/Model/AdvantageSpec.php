<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\Advantage;
use App\Domain\Model\Subscription;
use PhpSpec\ObjectBehavior;

class AdvantageSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Advantage::class);
        $this->getId()->shouldReturn(null);
    }

    public function it_should_save_a_message()
    {
        $this->setMessage('test name');
        $this->getMessage()->shouldReturn('test name');
    }

    public function it_should_save_a_code()
    {
        $this->setCode('SKB');
        $this->getCode()->shouldReturn('SKB');
    }

    public function it_should_save_a_priority()
    {
        $this->setPriority(1);
        $this->getPriority()->shouldReturn(1);
    }

    public function it_should_add_and_remove_subscription()
    {
        $subscription = new Subscription();
        $this->addSubscription($subscription);
        $this->getSubscriptions()->shouldContain($subscription);
        $this->getSubscriptions()->shouldHaveCount(1);
        $this->removeSubscription($subscription);
        $this->getSubscriptions()->shouldHaveCount(0);
    }
}
