<?php

namespace App\Domain\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ExclusionPolicy("all")
 */
class Comment
{
    /**
     * @var int
     * @Expose
     * @Groups({
     * })
     */
    private $id;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_comments",
     * })
     */
    private $title;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_comments",
     * })
     */
    private $message;

    /**
     * @var int
     * @Expose
     * @Groups({
     * "api_comments",
     * })
     */
    private $note;


    /**
     * @var Besoin
     * @Expose
     * @Groups({
     * })
     */
    private $besoin;
    /**
     * @var Company
     * @Expose
     * @Groups({
     * })
     */
    private $company;

    /**
     * @var Company
     * @Expose
     * @Groups({
     * "api_comments",
     * })
     */
    private $authorCompany;

    /**
     * @var Utilisateur
     * @Expose
     * @Groups({
     * "api_comments",
     * })
     */
    private $utilisateur;

    /**
     * @var DateTime
     * @Expose
     * @Groups({
     * "api_comments",
     * "api_dashboard_utilisateur"
     * })
     */
    private $dtCreated;



    public function __construct()
    {
    }

    /**
     * Get the value of id
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of title
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     * @param  string  $title
     * @return  self
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of message
     * @return  string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     * @param  string  $message
     * @return  self
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

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
    public function setNote(int $note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get the value of besoin
     *
     * @return  Besoin
     */
    public function getBesoin()
    {
        return $this->besoin;
    }

    /**
     * Set the value of besoin
     * @param  Besoin  $besoin
     * @return  self
     */
    public function setBesoin(Besoin $besoin)
    {
        $this->besoin = $besoin;
        return $this;
    }

    /**
     * Get the company
     *
     * @return  Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set the value of company
     *
     * @param  Company  $company
     *
     * @return  self
     */
    public function setCompany(?Company $company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Get the utilisateur
     *
     * @return  Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set the value of utilisateur
     *
     * @param  Utilisateur  $utilisateur
     *
     * @return  self
     */
    public function setUtilisateur(?Utilisateur $utilisateur)
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    /**
     * Get the authorCompany
     *
     * @return  Company
     */
    public function getAuthorCompany()
    {
        return $this->authorCompany;
    }

    /**
     * Set the value of authorCompany
     *
     * @param  Company  $authorCompany
     *
     * @return  self
     */
    public function setAuthorCompany(?Company $authorCompany)
    {
        $this->authorCompany = $authorCompany;
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
}
