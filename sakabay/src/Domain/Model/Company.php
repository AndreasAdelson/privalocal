<?php

namespace App\Domain\Model;

use App\Domain\Model\Utilisateur;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 * @UniqueEntity(
 *     fields={"name"},
 *     message="Une entreprise existe déjà avec ce nom"
 * )
 * @ExclusionPolicy("all")
 *
 */
class Company
{
    const PREFIX_ROLE = 'ROLE_';
    const SERVER_PATH_TO_IMAGE_FOLDER = '../../../sharedFiles';

    /**
     * @var int
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies",
     * "api_utilisateurs",
     * "api_dashboard_utilisateur",
     * "api_besoins",
     * "api_besoins_utilisateur"
     * })
     */
    private $id;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies",
     * "api_utilisateurs",
     * "api_dashboard_utilisateur",
     * "api_besoins_utilisateur",
     * "api_comments"
     * })
     */
    private $name;

    /**
     * @var integer
     * @Expose
     * @Groups({
     * "api_admin_companies",
     * })
     */
    private $numSiret;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies",
     * "api_dashboard_utilisateur",
     * "api_besoins_utilisateur",
     * "api_comments"
     * })
     */
    private $urlName;

    /**
     * @var Utilisateur
     * @Expose
     * @Groups({
     * "api_admin_companies"
     * })
     */
    private $utilisateur;

    /**
     * @Expose
     * @Groups({
     * "api_categories",
     * "api_admin_companies",
     * "api_companies"
     * })
     * @var Categorie
     *
     */
    private $category;

    /**
     * @Expose
     * @Groups({
     * "api_company_statut",
     * "api_admin_companies",
     * "api_dashboard_utilisateur"
     * })
     * @var CompanyStatut
     *
     */
    private $companyStatut;

    /**
     * @var Address
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies"
     * })
     */
    private $address;

    /**
     * @var City
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies"
     * })
     */
    private $city;

    /**
     * @var CompanySubscription[]
     * @Expose
     * @Groups({
     * "api_admin_companies",
     * "api_companies",
     * "api_dashboard_utilisateur"
     * })
     */
    private $companySubscriptions;

    /**
     * @var DateTime
     * @Expose
     * @Groups({
     * "api_admin_companies",
     * "api_dashboard_utilisateur"
     * })
     */
    private $dtCreated;

    /**
     * @var String
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies",
     * })
     */
    private $descriptionFull;

    /**
     * @var String
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies",
     * })
     */
    private $descriptionClean;

    /**
     * @var String
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies"
     * })
     */
    private $sousCategorys;

    /**
     * @var Answer[]
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies"
     * })
     */
    private $answers;

    /**
     * @var Besoin[]
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies"
     * })
     */
    private $besoins;

    /**
     * @var Besoin[]
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies"
     * })
     */
    private $besoinSelected;

    /**
     * @var Comment[]
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies"
     * })
     */
    private $comments;

    /**
     * @var Comment[]
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_admin_companies"
     * })
     */
    private $authorComments;

    /**
     * @var PaymentMethod[]
     * @Expose
     * @Groups({
     * "api_admin_companies",
     * "api_companies",
     * "api_dashboard_utilisateur"
     * })
     */
    private $paymentMethods;


    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_besoins_utilisateur",
     * "api_admin_companies",
     * "api_comments"
     * })
     */
    private $imageProfil;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_besoins_utilisateur",
     * "api_admin_companies"
     * })
     */
    private $stripeId;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_besoins_utilisateur",
     * "api_admin_companies"
     * })
     */
    private $email;

    /**
     * @var float
     * @Expose
     * @Groups({
     * "api_companies",
     * "api_besoins_utilisateur",
     * "api_admin_companies"
     * })
     */
    private $note;

    /**
     * Unmapped property to handle file uploads
     */
    private $file;



    public function __construct()
    {
        $this->companySubscriptions = new ArrayCollection();
        $this->paymentMethods = new ArrayCollection();
        $this->sousCategorys =  new ArrayCollection();
        $this->answers =  new ArrayCollection();
        $this->besoins =  new ArrayCollection();
        $this->besoinSelected =  new ArrayCollection();
        $this->comments =  new ArrayCollection();
        $this->authorComments =  new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get "api_companies"
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param  string  $name
     *
     * @return  self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     *
     * @return  string
     */
    public function getNumSiret()
    {
        return $this->numSiret;
    }

    /**
     *
     * @param  string  $numSiret
     *
     * @return  self
     */
    public function setNumSiret(string $numSiret)
    {
        $this->numSiret = $numSiret;

        return $this;
    }

    /**
     *
     * @return  string
     */
    public function getUrlName()
    {
        return $this->urlName;
    }

    /**
     * @param  string  $urlName
     * @return  self
     */
    public function setUrlName(string $urlName)
    {
        $this->urlName = $urlName;
        return $this;
    }


    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    /**
     *
     * @return  CompanyStatut
     */
    public function getCompanystatut()
    {
        return $this->companyStatut;
    }

    /**
     *
     * @param  CompanyStatut
     *
     * @return  self
     */
    public function setCompanystatut(CompanyStatut $companyStatut)
    {
        $this->companyStatut = $companyStatut;
        return $this;
    }

    /**
     * Get the value of address
     * @return  Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     * @param  Address  $address
     * @return  self
     */
    public function setAddress(?Address $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     *
     * @return  City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     *
     * @param  City
     *
     * @return  self
     */
    public function setCity(City $city)
    {
        $this->city = $city;
        return $this;
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
            $companySubscription->setCompany($this);
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

    public function getDtCreated(): ?\DateTimeInterface
    {
        return $this->dtCreated;
    }

    public function setDtCreated(\DateTimeInterface $dtCreated): self
    {
        $this->dtCreated = $dtCreated;

        return $this;
    }

    /**
     *
     * @return  string
     */
    public function getDescriptionFull()
    {
        return $this->descriptionFull;
    }

    /**
     * @param  string  $descriptionFull
     * @return  self
     */
    public function setDescriptionFull(string $descriptionFull)
    {
        $this->descriptionFull = $descriptionFull;
        return $this;
    }

    /**
     *
     * @return  string
     */
    public function getDescriptionClean()
    {
        return $this->descriptionClean;
    }

    /**
     * @param  string  $descriptionClean
     * @return  self
     */
    public function setDescriptionClean(string $descriptionClean)
    {
        $this->descriptionClean = $descriptionClean;
        return $this;
    }

    /**
     * @return Collection|SousCategory[]
     */
    public function getSousCategorys(): Collection
    {
        return $this->sousCategorys;
    }

    /**
     * Set the value of sousCategorys
     * @param  SousCategorys[]  $sousCategorys
     * @return  self
     */
    public function addSousCategory(SousCategory $sousCategory): self
    {
        if (
            !$this->sousCategorys->contains($sousCategory)
            && $sousCategory->getCategory()->getId() === $this->getCategory()->getId()
        ) {
            $this->sousCategorys[] = $sousCategory;
            $sousCategory->addCompany($this);
        }

        return $this;
    }


    public function removeSousCategory(SousCategory $sousCategory): self
    {
        if ($this->sousCategorys->contains($sousCategory)) {
            $this->sousCategorys->removeElement($sousCategory);
            $sousCategory->removeCompany($this);
        }
        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setCompany($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setCompany($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getAuthorComments(): Collection
    {
        return $this->authorComments;
    }

    public function addAuthorComment(Comment $authorComment): self
    {
        if (!$this->authorComments->contains($authorComment)) {
            $this->authorComments[] = $authorComment;
            $authorComment->setCompany($this);
        }

        return $this;
    }

    public function removeAuthorComment(Comment $authorComment): self
    {
        if ($this->authorComments->contains($authorComment)) {
            $this->authorComments->removeElement($authorComment);
        }

        return $this;
    }

    /**
     * @return Collection|Besoin[]
     */
    public function getBesoinSelecteds(): Collection
    {
        return $this->besoinSelected;
    }

    public function addBesoinSelected(Besoin $besoinSelected): self
    {
        if (!$this->besoinSelected->contains($besoinSelected)) {
            $this->besoinSelected[] = $besoinSelected;
            $besoinSelected->setCompany($this);
        }

        return $this;
    }

    public function removeBesoinSelected(Besoin $besoinSelected): self
    {
        if ($this->besoinSelected->contains($besoinSelected)) {
            $this->besoinSelected->removeElement($besoinSelected);
        }

        return $this;
    }

    /**
     * @return Collection|Besoin[]
     */
    public function getBesoins(): Collection
    {
        return $this->besoins;
    }

    public function addBesoin(Besoin $besoin): self
    {
        if (!$this->besoins->contains($besoin)) {
            $this->besoins[] = $besoin;
            $besoin->setCompany($this);
        }

        return $this;
    }

    public function removeBesoin(Besoin $besoin): self
    {
        if ($this->besoins->contains($besoin)) {
            $this->besoins->removeElement($besoin);
        }

        return $this;
    }

    /**
     * @return  string
     */
    public function getImageProfil()
    {
        return $this->imageProfil;
    }

    /**
     * @param  string  $imageProfil
     *
     * @return  self
     */
    public function setImageProfil(?string $imageProfil)
    {
        $this->imageProfil = $imageProfil;
        return $this;
    }

    /**
     * Get "api_companies"
     *
     * @return  string
     */
    public function getStripeId()
    {
        return $this->stripeId;
    }

    /**
     *
     * @param  string  $stripeId
     *
     * @return  self
     */
    public function setStripeId(?string $stripeId)
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    /**
     * Get "api_companies"
     *
     * @return  string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *
     * @param  string  $email
     *
     * @return  self
     */
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of note
     *
     * @return  int
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set the value of note
     * @param  int  $note
     * @return  self
     */
    public function setNote(float $note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection|PaymentMethod[]
     */
    public function getPaymentMethods(): Collection
    {
        return $this->paymentMethods;
    }

    public function addPaymentMethod(PaymentMethod $paymentMethod): self
    {
        if (!$this->paymentMethods->contains($paymentMethod)) {
            $this->paymentMethods[] = $paymentMethod;
            $paymentMethod->setCompany($this);
        }

        return $this;
    }

    public function removePaymentMethod(PaymentMethod $paymentMethod): self
    {
        if ($this->paymentMethods->contains($paymentMethod)) {
            $this->paymentMethods->removeElement($paymentMethod);
        }

        return $this;
    }
}
