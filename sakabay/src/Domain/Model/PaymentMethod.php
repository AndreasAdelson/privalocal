<?php

namespace App\Domain\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass=PaymentMethodRepository::class)
 * @Table(uniqueConstraints={@UniqueConstraint(columns={"company_id", "fingerprint"})})
 *
 * @ExclusionPolicy("all")
 */
class PaymentMethod
{
    /**
     * @var int
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies",
     * })
     */
    private $id;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_dashboard_utilisateur",
     * "api_companies",
     * "api_admin_companies",
     * })
     */
    private $stripeId;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_dashboard_utilisateur",
     * "api_companies",
     * "api_admin_companies",
     * })
     */
    private $last4;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_dashboard_utilisateur",
     * "api_companies",
     * "api_admin_companies",
     * })
     */
    private $fingerprint;

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
     * })
     */
    private $country;


    /**
     * @var Boolean
     * @Expose
     * @Groups({
     * "api_dashboard_utilisateur",
     * "api_companies",
     * "api_admin_companies",
     * })
     */
    private $defaultMethod;

    public function __construct()

    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStripeId()
    {
        return $this->stripeId;
    }

    public function setStripeId(string $stripeId)
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    public function getLast4()
    {
        return $this->last4;
    }

    public function setLast4(string $last4)
    {
        $this->last4 = $last4;

        return $this;
    }

    public function getFingerprint()
    {
        return $this->fingerprint;
    }

    public function setFingerprint(string $fingerprint): self
    {
        $this->fingerprint = $fingerprint;
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

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(string $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the defaultMethod
     *
     * @return  Bool
     */
    public function getDefaultMethod()
    {
        return $this->defaultMethod;
    }

    /**
     * Set the value of defaultMethod
     *
     * @param  Bool  $defaultMethod
     *
     * @return  self
     */
    public function setDefaultMethod(Bool $defaultMethod)
    {
        $this->defaultMethod = $defaultMethod;
        return $this;
    }
}
