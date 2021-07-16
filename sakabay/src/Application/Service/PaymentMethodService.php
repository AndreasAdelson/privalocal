<?php

namespace App\Application\Service;

use App\Domain\Model\PaymentMethod;
use App\Infrastructure\Repository\PaymentMethodRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;

class PaymentMethodService
{
    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * PaymentMethodRestController constructor.
     */
    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /// Afficher un PaymentMethod
    public function getPaymentMethod(int $paymentMethodId): ?PaymentMethod
    {
        return $this->paymentMethodRepository->find($paymentMethodId);
    }

    public function getAllPaymentMethods(): ?array
    {
        return $this->paymentMethodRepository->findAll();
    }

    public function getDefaultPaymentMethod($companyId): ?PaymentMethod
    {
        return $this->paymentMethodRepository->getDefaultPaymentMethod($companyId);
    }

    public function findPaymentMethodByStripeId($stripePaymentMethodId): ?PaymentMethod
    {
        $paymentMethod = $this->paymentMethodRepository->findOneBy([
            'stripeId' => $stripePaymentMethodId
        ]);
        if (!$paymentMethod) {
            throw new \Exception('Somehow we have no paymentMethod id ' . $stripePaymentMethodId);
        }

        return $paymentMethod;
    }

    public function unsetDefaultPaymentMethodByStripeId($stripePaymentMethodId): void
    {
        $paymentMethod = $this->findPaymentMethodByStripeId($stripePaymentMethodId);
        $this->paymentMethodRepository->delete($paymentMethod);

        // $paymentMethod = $this->findPaymentMethodByStripeId($stripePaymentMethodId);
        // $paymentMethod->setDefaultMethod(0);
        // $this->paymentMethodRepository->save($paymentMethod);
    }
}
