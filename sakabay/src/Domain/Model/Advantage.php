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
class Advantage
{
    /**
     * @var int
     * @Expose
     * @Groups({
     * "api_subscriptions",
     * "api_companies",
     * "api_advantages"
     * })
     */
    private $id;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_subscriptions",
     * "api_dashboard_utilisateur",
     * "api_advantages"
     * })
     */
    private $message;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_subscriptions",
     * "api_dashboard_utilisateur",
     * "api_advantages"
     * })
     */
    private $code;

    /**
     * @var integer
     * @Expose
     * @Groups({
     * "api_subscriptions",
     * "api_dashboard_utilisateur",
     * "api_advantages"
     * })
     */
    private $priority;

    /**
     * @var Subscription[]
     * @Expose
     * @Groups({
     * })
     */
    private $subscriptions;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
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
     * Get })
     *
     * @return  string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set })
     *
     * @param  string  $message  })
     *
     * @return  self
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

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
     * Get the value of priority
     * @return  int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set the value of priority
     * @param  int  $priority
     * @return  self
     */
    public function setPriority(int $priority)
    {
        $this->priority = $priority;

        return $this;
    }


    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->addAdvantage($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->contains($subscription)) {
            $this->subscriptions->removeElement($subscription);
            $subscription->removeAdvantage($this);
        }

        return $this;
    }
}
