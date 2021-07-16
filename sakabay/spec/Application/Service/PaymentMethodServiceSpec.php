<?php

namespace spec\App\Application\Service;

use App\Application\Service\PaymentMethodService;
use App\Domain\Model\PaymentMethod;
use App\Infrastructure\Repository\PaymentMethodRepository;
use Exception;
use PhpSpec\ObjectBehavior;

class PaymentMethodServiceSpec extends ObjectBehavior
{
    public function let(
        PaymentMethodRepository $paymentMethodRepository
    ) {
        $this->beConstructedWith($paymentMethodRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentMethodService::class);
    }

    public function it_should_get_payment_method($paymentMethodRepository)
    {
        $paymentMethodRepository->find(0)->willReturn(null);
        $this->getPaymentMethod(0)->shouldReturn(null);
        $paymentMethodRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_payment_method($paymentMethodRepository)
    {
        $paymentMethodRepository->findAll()->willReturn([]);
        $this->getAllPaymentMethods()->shouldReturn([]);
        $paymentMethodRepository->findAll()->shouldBeCalled();
    }

    public function it_should_get_default_paymentMethod($paymentMethodRepository)
    {
        $paymentMethodRepository->getDefaultPaymentMethod(0)->willReturn(null);
        $this->getDefaultPaymentMethod(0)->shouldReturn(null);
        $paymentMethodRepository->getDefaultPaymentMethod(0)->shouldBeCalled();
    }

    public function it_should_find_payment_method_by_stripe_id($paymentMethodRepository)
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethodRepository->findOneBy(['stripeId' => 0])->willReturn($paymentMethod);
        $this->findPaymentMethodByStripeId(0)->shouldReturn($paymentMethod);
        $paymentMethodRepository->findOneBy(['stripeId' => 0])->shouldBeCalled();
    }

    public function it_should_throw_error_when_trying_to_find_non_existant_payment_method_by_strie_id($paymentMethodRepository)
    {
        $paymentMethodRepository->findOneBy(['stripeId' => 0])->willReturn(null);

        $expectedException = new Exception('Somehow we have no paymentMethod id 0');
        $this->shouldThrow($expectedException)->during('findPaymentMethodByStripeId', [0]);
    }

    public function it_should_unset_payment_method_by_stripe_id($paymentMethodRepository)
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethodRepository->findOneBy(['stripeId' => 0])->willReturn($paymentMethod);
        $this->unsetDefaultPaymentMethodByStripeId(0)->shouldReturn(null);
        $paymentMethodRepository->findOneBy(['stripeId' => 0])->shouldBeCalled();
        $paymentMethodRepository->delete($paymentMethod)->shouldBeCalled();
    }

    public function it_should_throw_error_when_trying_to_unset_non_existant_payment_method_by_strie_id($paymentMethodRepository)
    {
        $paymentMethodRepository->findOneBy(['stripeId' => 0])->willReturn(null);

        $expectedException = new Exception('Somehow we have no paymentMethod id 0');
        $this->shouldThrow($expectedException)->during('unsetDefaultPaymentMethodByStripeId', [0]);
    }
}
