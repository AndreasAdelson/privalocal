<?php

namespace spec\App\Infrastructure\Security;

use App\Infrastructure\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class LoginFormAuthenticatorSpec extends ObjectBehavior
{
    // public function let(EntityManager $entityManager, UrlGenerator $urlGenerator, CsrfTokenManager $csrfTokenManager, UserPasswordEncoder $userPasswordEncoder)
    // {
    //     $this->beConstructedWith($entityManager, $urlGenerator, $csrfTokenManager, $userPasswordEncoder);
    // }

    // public function it_is_initializable()
    // {
    //     $this->shouldHaveType(LoginFormAuthenticator::class);
    // }

    // public function it_should_get_credentials(
    //     Request $request,
    //     Session $session
    // ) {

    //     $request->request->set('email', 'testàtest.com');
    //     $request->get(Argument::any())->willReturn('test');

    //     $request->getSession()->willReturn($session);
    //     $session->set(Argument::any(), Argument::any())->shouldReturn(null);
    //     $this->getCredentials($request);
    // }
}
