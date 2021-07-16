<?php

namespace App\Domain\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=CompanySubscriptionRepository::class)
 * @ExclusionPolicy("all")
 */
class CompanySubscription
{
    /**
     * @var int
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies",
     * "api_company_subscriptions"
     * })
     */
    private $id;

    /**
     * @var DateTime
     * @Expose
     * @Groups({
     * "api_dashboard_utilisateur",
     * "api_companies",
     * "api_admin_companies",
     * "api_company_subscriptions"
     * })
     */
    private $dtDebut;

    /**
     * @var DateTime
     * @Expose
     * @Groups({
     * "api_dashboard_utilisateur",
     * "api_companies",
     * "api_admin_companies",
     * "api_company_subscriptions"
     * })
     */
    private $dtFin;

    /**
     * @var Subscription
     * @Expose
     * @Groups({
     * "api_dashboard_utilisateur",
     * "api_admin_companies",
     * "api_companies",
     * })
     */
    private $subscription;

    /**
     * @var Company
     * @Expose
     * @Groups({
     * "api_dashboard_utilisateur",
     * "api_companies",
     * "api_admin_companies",
     * })
     */
    private $company;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_dashboard_utilisateur",
     * "api_companies",
     * "api_admin_companies",
     * "api_company_subscriptions"
     * })
     */
    private $stripeId;

    /**
     * @Expose
     * @Groups({
     * "api_subscription_status",
     * "api_admin_companies",
     * "api_dashboard_utilisateur",
     * "api_company_subscriptions"
     * })
     * @var SubscriptionStatus
     *
     */
    private $subscriptionStatus;


    public function __construct()

    {
    }

    /**
     * Get })
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get })
     *
     * @return  DateTime
     */
    public function getDtDebut()
    {
        return $this->dtDebut;
    }

    /**
     * Set })
     *
     * @param  DateTime  $dtDebut  })
     *
     * @return  self
     */
    public function setDtDebut(DateTime $dtDebut)
    {
        $this->dtDebut = $dtDebut;

        return $this;
    }

    /**
     * Get })
     *
     * @return  DateTime
     */
    public function getDtFin()
    {
        return $this->dtFin;
    }

    /**
     * Set })
     *
     * @param  DateTime  $dtFin  })
     *
     * @return  self
     */
    public function setDtFin(DateTime $dtFin)
    {
        $this->dtFin = $dtFin;

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;
        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getStripeId()
    {
        return $this->stripeId;
    }

    public function setStripeId(?string $stripeId)
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    /**
     *
     * @return  SubscriptionStatus
     */
    public function getSubscriptionStatus()
    {
        return $this->subscriptionStatus;
    }

    /**
     *
     * @param  SubscriptionStatus
     *
     * @return  self
     */
    public function setSubscriptionStatus(SubscriptionStatus $subscriptionStatus)
    {
        $this->subscriptionStatus = $subscriptionStatus;
        return $this;
    }
}
