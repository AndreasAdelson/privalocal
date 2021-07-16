<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\PaymentMethod;
use App\Domain\Model\Company;
use PhpSpec\ObjectBehavior;

class PaymentMethodSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentMethod::class);
        $this->getId()->shouldReturn(null);
    }

    public function it_should_save_a_stripe_id()
    {
        $this->setStripeId('test stripe id');
        $this->getStripeId()->shouldReturn('test stripe id');
    }

    public function it_should_save_a_last4()
    {
        $this->setLast4('1234');
        $this->getLast4()->shouldReturn('1234');
    }

    public function it_should_save_a_finger_print()
    {
        $this->setfingerPrint('1234');
        $this->getfingerPrint()->shouldReturn('1234');
    }

    public function it_should_save_a_country()
    {
        $this->setCountry('FR');
        $this->getCountry()->shouldReturn('FR');
    }

    public function it_should_save_a_company()
    {
        $company = new Company();
        $this->setCompany($company);
        $this->getCompany()->shouldReturn($company);
    }

    public function it_should_save_a_default_method()
    {
        $this->setDefaultMethod(true);
        $this->getDefaultMethod()->shouldReturn(true);
    }
}
