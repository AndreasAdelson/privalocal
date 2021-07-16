<?php

namespace spec\App\Domain\Model;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;
use App\Domain\Model\Utilisateur;
use DateTime;
use PhpSpec\ObjectBehavior;

class ResetPasswordRequestSpec extends ObjectBehavior
{
    private $utilisateur;

    public function let(Utilisateur $utilisateur)
    {
        $this->utilisateur = $utilisateur;
        $selector = 'selector';
        $hashedToken = 'hashed token';
        $dtExpiresAt = new \DateTime('2018-01-03 00:05:30');
        $this->beConstructedWith($utilisateur, $dtExpiresAt, $selector, $hashedToken);
    }

    public function it_is_initializable()
    {
        $this->getId()->shouldReturn(null);
    }


    public function it_should_save_a_expires_at()
    {
        $this->setExpiresAt(new \DateTime('2018-01-03 00:05:30'));
        $this->getExpiresAt()->format('d-m-Y')->shouldReturn('03-01-2018');
    }

    public function it_should_save_a_requested_at()
    {
        $this->setRequestedAt(new \DateTime('2018-01-03 00:05:30'));
        $this->getRequestedAt()->format('d-m-Y')->shouldReturn('03-01-2018');
    }

    public function it_should_get_user()
    {
        $this->getUser()->shouldReturn($this->utilisateur);
    }
}
