<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\Besoin;
use App\Domain\Model\Comment;
use App\Domain\Model\Company;
use App\Domain\Model\Utilisateur;
use PhpSpec\ObjectBehavior;

class CommentSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Comment::class);
        $this->getId()->shouldReturn(null);
    }

    public function it_should_save_a_title()
    {
        $this->setTitle('test title');
        $this->getTitle()->shouldReturn('test title');
    }

    public function it_should_save_a_message()
    {
        $this->setMessage('test message');
        $this->getMessage()->shouldReturn('test message');
    }

    public function it_should_save_a_note()
    {
        $this->setNote(1);
        $this->getNote()->shouldReturn(1);
    }

    public function it_should_save_a_besoin()
    {
        $besoin = new Besoin();
        $this->setBesoin($besoin);
        $this->getBesoin()->shouldReturn($besoin);
    }

    public function it_should_save_a_company()
    {
        $company = new Company();
        $this->setCompany($company);
        $this->getCompany()->shouldReturn($company);
    }

    public function it_should_save_a_utilisateur()
    {
        $utilisateur = new Utilisateur();
        $this->setUtilisateur($utilisateur);
        $this->getUtilisateur()->shouldReturn($utilisateur);
    }

    public function it_should_save_a_author_company()
    {
        $authorCompany = new Company();
        $this->setAuthorCompany($authorCompany);
        $this->getAuthorCompany()->shouldReturn($authorCompany);
    }

    public function it_should_save_a_dt_created()
    {
        $this->setDtCreated(new \DateTime('2018-01-03 00:05:30'));
        $this->getDtCreated()->format('d-m-Y')->shouldReturn('03-01-2018');
    }
}
