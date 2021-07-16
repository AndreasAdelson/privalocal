<?php

namespace spec\App\Application\Service;

use App\Application\Service\UtilisateurService;
use App\Domain\Model\Utilisateur;
use App\Infrastructure\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UtilisateurServiceSpec extends ObjectBehavior
{
    public function let(
        UtilisateurRepository $utilisateurRepository
    ) {
        $this->beConstructedWith($utilisateurRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(UtilisateurService::class);
    }

    public function it_should_get_utilisateur($utilisateurRepository)
    {
        $utilisateurRepository->find(0)->willReturn(null);
        $this->getUtilisateur(0)->shouldReturn(null);
        $utilisateurRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_utilisateur($utilisateurRepository)
    {
        $utilisateurRepository->findAll()->willReturn([]);
        $this->getAllUtilisateurs()->shouldReturn([]);
        $utilisateurRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteUtilisateur(int $utilisateurId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($utilisateurRepository)
    {
        $utilisateurRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Utilisateur with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteUtilisateur', [0]);
    }

    public function it_should_delete_utilisateur($utilisateurRepository)
    {
        $utilisateur = new Utilisateur();
        $utilisateurRepository->find(0)->willReturn($utilisateur);
        $utilisateurRepository->delete(Argument::any());
        $this->deleteUtilisateur(0);
        $utilisateurRepository->find(0)->shouldBeCalled();
        $utilisateurRepository->delete($utilisateur)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($utilisateurRepository)
    {
        $utilisateurRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $utilisateurRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }

    public function it_should_find_users_for_autocomplete($utilisateurRepository)
    {
        $utilisateurRepository->findUsersForAutocomplete([])->willReturn([]);
        $this->findUsersForAutocomplete([])->shouldReturn([]);
        $utilisateurRepository->findUsersForAutocomplete([])->shouldBeCalled();
    }

    public function it_should_find_users_by_right($utilisateurRepository)
    {
        $utilisateurRepository->findUsersByRight([], true)->willReturn([]);
        $this->findUsersByRight([], true)->shouldReturn([]);
        $utilisateurRepository->findUsersByRight([], true)->shouldBeCalled();
    }
}
