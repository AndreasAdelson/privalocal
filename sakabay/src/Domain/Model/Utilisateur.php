<?php

namespace App\Domain\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Domain\Model\Company;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use Mgilet\NotificationBundle\Annotation\Notifiable;
use Mgilet\NotificationBundle\NotifiableInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Un compte utilise déjà cet email"
 * )
 * @ExclusionPolicy("all")
 * @Notifiable(name="Utilisateur")
 */
class Utilisateur implements UserInterface, EquatableInterface, NotifiableInterface
{

    const PREFIX_ROLE = 'ROLE_';
    const SERVER_PATH_TO_IMAGE_FOLDER = '../../../sharedFiles';

    /**
     * @var integer
     * @Expose
     * @Groups({
     * "api_utilisateurs",
     * "api_groups",
     * "api_admin_companies",
     * "api_dashboard_utilisateur",
     * "api_besoins_utilisateur"
     * })
     */
    private $id;

    /**
     * @var string|null
     * @Expose
     * @Groups({
     * "api_utilisateurs",
     * "api_groups",
     * "api_dashboard_utilisateur",
     * "api_admin_companies"
     * })
     */
    private $firstName;


    /**
     * @var string|null
     * @Expose
     * @Groups({
     * })
     */
    private $plainPassword;

    /**
     * @var string|null
     * @Expose
     * @Groups({
     * "api_utilisateurs",
     * "api_groups",
     * "api_dashboard_utilisateur",
     * "api_admin_companies"
     * })
     */
    private $lastName;

    /**
     * @var string
     * @Assert\Email()
     * @Expose
     * @Groups({
     * "api_utilisateurs",
     * "api_groups",
     * "api_dashboard_utilisateur",
     * })
     */
    private $email;


    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_utilisateurs",
     * "api_groups",
     * "api_admin_companies",
     * "api_dashboard_utilisateur",
     * "api_comments"
     * })
     */
    private $username;

    /**
     * @Expose
     * @Groups({
     * })
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @Expose
     * @Groups({
     * })
     */
    private $password;

    /**
     * @var Group[]
     * @Expose
     * @Groups({
     * })
     */
    private $groups;

    /**
     * @var string
     * @Expose
     * @Groups({
     * "api_utilisateurs",
     * "api_groups",
     * "api_dashboard_utilisateur",
     * "api_admin_companies",
     * "api_comments"
     * })
     */
    private $imageProfil;

    /**
     * @var Company[]
     * @Expose
     * @Groups({
     * "api_utilisateurs",
     * "api_dashboard_utilisateur"
     * })
     */
    private $companys;

    /**
     * @var Besoin[]
     * @Expose
     * @Groups({
     * "api_utilisateurs",
     * "api_dashboard_utilisateur"
     * })
     */
    private $besoins;

    /**
     * @var Comment[]
     * @Expose
     * @Groups({
     * "api_utilisateurs",
     * "api_dashboard_utilisateur"
     * })
     */
    private $comments;

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * Unmapped property to handle file uploads
     */
    private $selector;


    /**
     * @var ResetPasswordRequest
     * @Expose
     * @Groups({
     * })
     */
    private $reset_password;


    /**
     * @var DateTime
     *
     */
    private $updated;


    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->companys = new ArrayCollection();
        $this->besoins = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $name): self
    {
        $this->firstName = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $name): self
    {
        $this->lastName = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = mb_strtoupper($username);

        return $this;
    }

    /**
     * @see UtilisateurInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UtilisateurInterface
     */
    public function getSalt()
    {
        // not needed when using the "auto" algorithm in security.yaml
    }

    /**
     * @see UtilisateurInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get the value of plainPassword
     *
     * @return  string|null
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     *
     * @param  string|null  $plainPassword
     *
     * @return  self
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $this->addRolesOfGroup($group);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $this->updateRoles();
        }

        return $this;
    }

    /**
     * Return the security roles of the users. Security roles are the user's functions.
     * Return an array of all user's functions in uppercase prefixed by 'ROLE_'. This
     * array does not contain duplication.
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        $securityRoles = [];

        foreach ($this->getGroups() as $group) {
            foreach ($group->getRoles() as $role) {
                foreach ($role->getFonctions() as $fonction) {
                    $securityRoleName = Utilisateur::PREFIX_ROLE . mb_strtoupper($fonction->getCode());
                    $securityRoles[] = $securityRoleName;
                }
            }
        }
        // if ($this->getDefaultRole()) {
        //     foreach ($this->getDefaultRole()->getFonctions() as $fonction) {
        //         $securityRoleName = Utilisateur::PREFIX_ROLE.mb_strtoupper($fonction->getCode());
        //         $securityRoles[] = $securityRoleName;
        //     }
        // }

        return array_values(array_unique($securityRoles));
    }

    /**
     * Called when a group is added to the user.
     * Add the functions of the group to the user's security roles.
     */
    private function addRolesOfGroup($group): void
    {
        foreach ($group->getRoles() as $role) {
            foreach ($role->getFonctions() as $fonction) {
                $securityRoleName = Utilisateur::PREFIX_ROLE . mb_strtoupper($fonction->getCode());
                if (!in_array($securityRoleName, $this->roles)) {
                    $this->roles[] = $securityRoleName;
                }
            }
        }
    }

    /**
     * Called when a group is deleted from the groups list.
     * Remove the roles from this group which were not in other groups of the user.
     */
    private function updateRoles(): void
    {
        $tmpRoles = [];
        foreach ($this->getGroups() as $group) {
            foreach ($group->getRoles() as $role) {
                foreach ($role->getFonctions() as $fonction) {
                    $securityRoleName = Utilisateur::PREFIX_ROLE . mb_strtoupper($fonction->getCode());
                    $tmpRoles[] = $securityRoleName;
                }
            }
        }
        $this->roles = array_values(array_unique($tmpRoles));
    }

    public function hasGroup($groupCode): bool
    {
        foreach ($this->getGroups() as $group) {
            if ($group->getCode() == $groupCode) {
                return true;
            }
        }

        return false;
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
     * @return Collection|Company[]
     */
    public function getCompanys(): Collection
    {
        return $this->companys;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companys->contains($company)) {
            $this->companys[] = $company;
            $company->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companys->contains($company)) {
            $this->companys->removeElement($company);
        }

        return $this;
    }

    /**
     * Get the value of updated
     *
     * @return  DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set the value of updated
     *
     * @param  DateTime  $updated
     *
     * @return  self
     */
    public function setUpdated(\DateTimeInterface $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire.
     */
    public function refreshUpdated()
    {
        $this->setUpdated(new \DateTime());
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
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
            $besoin->setAuthor($this);
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
            $comment->setUtilisateur($this);
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
}
