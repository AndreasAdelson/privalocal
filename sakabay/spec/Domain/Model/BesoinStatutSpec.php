<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\BesoinStatut;
use App\Domain\Model\Besoin;
use PhpSpec\ObjectBehavior;

class BesoinStatutSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(BesoinStatut::class);
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

    public function it_should_save_a_priority()
    {
        $this->setPriority(1234);
        $this->getPriority()->shouldReturn(1234);
    }

    public function it_should_add_and_remove_besoin()
    {
        $besoin = new Besoin();
        $this->addBesoin($besoin);
        $this->getBesoins()->shouldContain($besoin);
        $this->getBesoins()->shouldHaveCount(1);
        $this->removeBesoin($besoin);
        $this->getBesoins()->shouldHaveCount(0);
    }
}
