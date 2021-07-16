<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\Besoin;
use App\Domain\Model\Comment;
use App\Domain\Model\Company;
use App\Domain\Model\Fonction;
use App\Domain\Model\Group;
use App\Domain\Model\Role;
use App\Domain\Model\Utilisateur;
use DateTime;
use PhpSpec\ObjectBehavior;

class UtilisateurSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Utilisateur::class);
        $this->getId()->shouldReturn(null);
        $this->getUsername()->shouldReturn(null);
        $this->getPassword()->shouldReturn("");
        $this->getSalt()->shouldReturn(null);
    }

    public function it_should_save_a_first_name()
    {
        $this->setFirstName('test name');
        $this->getFirstName()->shouldReturn('test name');
    }

    public function it_should_save_a_last_name()
    {
        $this->setLastName('test name');
        $this->getLastName()->shouldReturn('test name');
    }

    public function it_should_save_an_email()
    {
        $this->setEmail('SKB@skb.com');
        $this->getEmail()->shouldReturn('SKB@skb.com');
    }

    public function it_should_save_a_username()
    {
        $this->setUserName('test name');
        $this->getUserName()->shouldReturn('TEST NAME');
    }

    public function it_should_save_a_password()
    {
        $this->getPassword()->shouldReturn("");
        $this->setPassword('test password');
        $this->getPassword()->shouldReturn('test password');
    }

    public function it_should_save_a_plain_password()
    {
        $this->getPlainPassword()->shouldReturn(null);
        $this->setPlainPassword('test password');
        $this->getPlainPassword()->shouldReturn('test password');
    }

    public function it_should_add_and_remove_company()
    {
        $company = new Company();
        $this->addCompany($company);
        $this->getCompanys()->shouldContain($company);
        $this->getCompanys()->shouldHaveCount(1);
        $this->removeCompany($company);
        $this->getCompanys()->shouldHaveCount(0);
    }

    public function it_should_add_and_remove_groups()
    {
        $group = new Group();
        $this->addGroup($group);
        $this->getGroups()->shouldContain($group);
        $this->getGroups()->shouldHaveCount(1);
        $this->removeGroup($group);
        $this->getGroups()->shouldHaveCount(0);
    }

    public function it_should_save_updated()
    {
        $dateTime = new \DateTime();
        $this->getUpdated()->shouldReturn(null);
        $this->setUpdated($dateTime);
        $this->getUpdated()->shouldReturn($dateTime);
        $this->refreshUpdated();
    }

    /**
     * Test of the function addRolesOfGroup.
     * When adding a group, check if all the functions of this group are
     * added to the user's (security) roles.
     */
    public function it_should_add_roles_of_group()
    {
        $fonction1 = new Fonction();
        $fonction1->setCode('fonction1');
        $fonction2 = new Fonction();
        $fonction2->setCode('fonction2');
        $fonction3 = new Fonction();
        $fonction3->setCode('fonction3');

        $role_1 = new Role();
        $role_1->setCode('role_1');
        $role_1->addFonction($fonction1);
        $role_2_3 = new Role();
        $role_2_3->setCode('role_2_3');
        $role_2_3->addFonction($fonction2);
        $role_2_3->addFonction($fonction3);

        $group_1_2_3 = new Group();
        $group_1_2_3->setCode('group_1_2');
        $group_1_2_3->addRole($role_1);
        $group_1_2_3->addRole($role_2_3);

        $this->addGroup($group_1_2_3);
        $this->getRoles()->shouldEqual(['ROLE_FONCTION1', 'ROLE_FONCTION2', 'ROLE_FONCTION3']);
    }

    /**
     * Check if the function getRoles does return the list of the user's functions
     * in uppercase with the prefix 'ROLE_' added.
     */
    public function it_should_get_roles()
    {
        $fonction1 = new Fonction();
        $fonction1->setCode('fonction1');
        $fonction2 = new Fonction();
        $fonction2->setCode('fonction2');
        $fonction3 = new Fonction();
        $fonction3->setCode('fonction3');
        $fonction_4 = new Fonction();
        $fonction_4->setCode('fonction4');
        $fonction_5 = new Fonction();
        $fonction_5->setCode('fonction5');

        $role_1 = new Role();
        $role_1->setCode('role_1');
        $role_1->addFonction($fonction1);
        $role_2_3 = new Role();
        $role_2_3->setCode('role_2_3');
        $role_2_3->addFonction($fonction2);
        $role_2_3->addFonction($fonction3);
        $role_4_5 = new Role();
        $role_4_5->setCode('role_4_5');
        $role_4_5->addFonction($fonction_4);
        $role_4_5->addFonction($fonction_5);

        $group_1_2_3 = new Group();
        $group_1_2_3->setCode('group_1_2_3');
        $group_1_2_3->addRole($role_1);
        $group_1_2_3->addRole($role_2_3);
        $group_4_5 = new Group();
        $group_4_5->setCode('group_4_5');
        $group_4_5->addRole($role_4_5);

        $this->addGroup($group_1_2_3);
        $this->addGroup($group_4_5);
        $this->getRoles()->shouldEqual(['ROLE_FONCTION1', 'ROLE_FONCTION2', 'ROLE_FONCTION3', 'ROLE_FONCTION4', 'ROLE_FONCTION5']);
    }

    /**
     * Test of the function updateRoles.
     * Check if, after deleting a group, the functions present only in the
     * deleted group are removed from the user's (security) roles.
     */
    public function it_should_update_roles()
    {
        $fonction1 = new Fonction();
        $fonction1->setCode('fonction1');
        $fonction2 = new Fonction();
        $fonction2->setCode('fonction2');
        $fonction3 = new Fonction();
        $fonction3->setCode('fonction3');

        $role_1 = new Role();
        $role_1->setCode('role_1');
        $role_1->addFonction($fonction1);
        $role_2 = new Role();
        $role_2->setCode('role_2');
        $role_2->addFonction($fonction2);
        $role_3 = new Role();
        $role_3->setCode('role_3');
        $role_3->addFonction($fonction3);

        $group_1_2 = new Group();
        $group_1_2->setCode('group_1_2');
        $group_1_2->addRole($role_1);
        $group_1_2->addRole($role_2);
        $group_2_3 = new Group();
        $group_2_3->setCode('group_2_3');
        $group_2_3->addRole($role_2);
        $group_2_3->addRole($role_3);

        $this->addGroup($group_1_2);
        $this->addGroup($group_2_3);
        $this->getRoles()->shouldEqual(['ROLE_FONCTION1', 'ROLE_FONCTION2', 'ROLE_FONCTION3']);
        $this->removeGroup($group_2_3);
        $this->getRoles()->shouldEqual(['ROLE_FONCTION1', 'ROLE_FONCTION2']);
    }


    public function it_should_save_a_image_profil()
    {
        $this->setImageProfil('test name');
        $this->getImageProfil()->shouldReturn('test name');
    }

    public function it_should_erase_credentials()
    {
        $this->eraseCredentials()->shouldReturn(null);
    }

    public function it_has_role()
    {
        $this->getGroups()->shouldHaveCount(0);
        $group = new Group();
        $group->setCode('ADM');
        $group->setName('ADMINISTOR');
        $this->addGroup($group);
        $this->getGroups()->shouldHaveCount(1);
        $this->hasGroup('ADM')->shouldReturn(true);
        $this->hasGroup('CTR')->shouldReturn(false);
        $group = new Group();
        $group->setCode('CTR');
        $group->setName('Contributeur');
        $this->addGroup($group);
        $this->hasGroup('ADM')->shouldReturn(true);
        $this->hasGroup('CTR')->shouldReturn(true);
    }

    public function it_should_compare_two_users()
    {
        $user = new Utilisateur();
        $user->setUsername('René');

        $user2 = new Utilisateur();
        $user2->setUsername('Bernard');

        $user3 = new Utilisateur();
        $user3->setUsername('René');

        $user->isEqualTo($user3) === true;
        $user->isEqualTo($user2) === false;
    }

    public function it_should_add_and_remove_besoin()
    {
        $besoin = new Besoin();
        $this->addBesoin($besoin);
        $this->getBesoins()->shouldContain($besoin);
        $this->getBesoins()->shouldHaveCount(1);
        $this->removeBesoin($besoin);
        $this->getBesoins()->shouldHaveCount(0);
    }

    public function it_should_add_and_remove_comment()
    {
        $comment = new Comment();
        $this->addComment($comment);
        $this->getComments()->shouldContain($comment);
        $this->getComments()->shouldHaveCount(1);
        $this->removeComment($comment);
        $this->getComments()->shouldHaveCount(0);
    }
}
