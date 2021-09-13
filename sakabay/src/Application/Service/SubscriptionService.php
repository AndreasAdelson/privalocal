<?php

namespace App\Application\Service;

use App\Domain\Model\Subscription;
use App\Infrastructure\Repository\SubscriptionRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubscriptionService
{
    /**
     * @var SubscriptionRepositoryInterface
     */
    private $subscriptionRepository;

    /**
     * SubscriptionRestController constructor.
     */
    public function __construct(SubscriptionRepositoryInterface $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /// Afficher un Subscription
    public function getSubscription(int $subscriptionId): ?Subscription
    {
        return $this->subscriptionRepository->find($subscriptionId);
    }

    public function getAllSubscriptions(): ?array
    {
        return $this->subscriptionRepository->findBy([], ['price' => 'ASC']);
    }

    public function getSubscriptionByName(string $name)
    {
        return $this->subscriptionRepository->findOneBy(['name' => $name]);
    }

    public function getSubscriptionByCode(string $code)
    {
        return $this->subscriptionRepository->findOneBy(['code' => $code]);
    }

    public function getSubscriptionByStripeId(string $stripeId)
    {
        return $this->subscriptionRepository->findOneBy(['stripeId' => $stripeId]);
    }

    public function deleteSubscription(int $subscriptionId): void
    {
        $subscription = $this->subscriptionRepository->find($subscriptionId);
        if (!$subscription) {
            throw new EntityNotFoundException('Subscription with id ' . $subscriptionId . ' does not exist!');
        }
        $this->subscriptionRepository->delete($subscription);
    }

    /**
     * Retourne une page, potentiellement triée et filtrée.
     *
     *
     * @param string $sortBy
     * @param bool   $descending
     * @param string $filterFields
     * @param string $filterText
     * @param int    $currentPage
     * @param int    $perPage
     *
     * @return Pagerfanta
     */
    public function getPaginatedList(
        $sortBy = 'id',
        $descending = false,
        $filterFields = '',
        $filterText = '',
        $currentPage = 1,
        $perPage = PHP_INT_MAX ? PHP_INT_MAX : 10
    ) {
        return $this->subscriptionRepository
            ->getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage);
    }
}
