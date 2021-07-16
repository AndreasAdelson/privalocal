<?php

namespace App\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=SubscriptionStatusRepository::class)
 *
 * @ExclusionPolicy("all")
 *
 */
class SubscriptionStatus
{
    /**
     * @var int
     * @Expose
     * @Groups({
     * "api_subscription_status",
     * "api_admin_companies",
     * "api_companies",
     * "api_company_subscriptions"
     * })
     */
    private $id;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_subscription_status",
     * "api_admin_companies",
     * "api_company_subscriptions",
     * "api_companies"
     * })
     */
    private $name;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_subscription_status",
     * "api_admin_companies",
     * "api_companies",
     * "api_dashboard_utilisateur"
     * })
     */
    private $code;

    /**
     * @var CompanySubscription[]
     * @Expose
     * @Groups({
     * })
     */
    private $companySubscriptions;

    public function __construct()

    {
        $this->companySubscriptions = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of name
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     * @param  string  $name
     * @return  self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of code
     * @return  string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value of code
     * @param  string  $code
     * @return  self
     */
    public function setCode(string $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return CompanySubscription[]
     */
    public function getCompanySubscriptions(): Collection
    {
        return $this->companySubscriptions;
    }

    public function addCompanySubscription(CompanySubscription $companySubscription): self
    {
        if (!$this->companySubscriptions->contains($companySubscription)) {
            $this->companySubscriptions[] = $companySubscription;
            $companySubscription->setSubscriptionStatus($this);
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
}
