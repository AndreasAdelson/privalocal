<?php

namespace App\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 * @ExclusionPolicy("all")
 */
class Subscription
{
    /**
     * @var int
     * @Expose
     * @Groups({
     * "api_subscriptions",
     * "api_companies",
     * })
     */
    private $id;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_subscriptions",
     * })
     */
    private $code;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_subscriptions",
     * "api_admin_companies",
     * "api_dashboard_utilisateur"
     * })
     */
    private $name;

    /**
     * @var integer
     * @Expose
     * @Groups({
     * "api_subscriptions",
     * "api_admin_companies",
     * "api_dashboard_utilisateur"
     * })
     */
    private $price;


    /**
     * @var Companysubscription[]
     * @Expose
     * @Groups({
     * })
     */
    private $companySubscriptions;

    /**
     * @var Advantage[]
     * @Expose
     * @Groups({
     * "api_subscriptions",
     * })
     */
    private $advantages;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_subscriptions",
     * "api_admin_companies",
     * "api_dashboard_utilisateur"
     * })
     */
    private $stripeId;

    public function __construct()
    {
        $this->companySubscriptions = new ArrayCollection();
        $this->advantages = new ArrayCollection();
    }

    /**
     * Get })
     *
     * @return  integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set })
     *
     * @param  integer  $price  })
     *
     * @return  self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get })
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set })
     *
     * @param  string  $name  })
     *
     * @return  self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get })
     *
     * @return  string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set })
     *
     * @param  string  $code  })
     *
     * @return  self
     */
    public function setCode(string $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get "api_subscriptions",
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|CompanySubscription[]
     */
    public function getCompanySubscriptions(): Collection
    {
        return $this->companySubscriptions;
    }

    public function addCompanySubscription(CompanySubscription $companySubscription): self
    {
        if (!$this->companySubscriptions->contains($companySubscription)) {
            $this->companySubscriptions[] = $companySubscription;
            $companySubscription->setSubscription($this);
        }

        return $this;
    }

    public function removeCompanySubscription(CompanySubscription $companySubscription): self
    {
        if ($this->companySubscriptions->contains($companySubscription)) {
            $this->companySubscriptions->removeElement($companySubscription);
        }

        return $this;
    }

    /**
     * @return Collection|Advantage[]
     */
    public function getAdvantages(): Collection
    {
        return $this->advantages;
    }

    public function addAdvantage(Advantage $advantage): self
    {
        if (!$this->advantages->contains($advantage)) {
            $this->advantages[] = $advantage;
        }

        return $this;
    }

    public function removeAdvantage(Advantage $advantage): self
    {
        if ($this->advantages->contains($advantage)) {
            $this->advantages->removeElement($advantage);
        }

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
}
