<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\Answer;
use App\Domain\Model\Besoin;
use App\Domain\Model\BesoinStatut;
use App\Domain\Model\Category;
use App\Domain\Model\Comment;
use App\Domain\Model\Company;
use App\Domain\Model\SousCategory;
use App\Domain\Model\Utilisateur;
use PhpSpec\ObjectBehavior;

class BesoinSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Besoin::class);
        $this->getId()->shouldReturn(null);
    }

    public function it_should_save_a_title()
    {
        $this->setTitle('test title');
        $this->getTitle()->shouldReturn('test title');
    }

    public function it_should_save_a_description()
    {
        $this->setDescription('test description');
        $this->getDescription()->shouldReturn('test description');
    }

    public function it_should_save_a_dt_created()
    {
        $this->setDtCreated(new \DateTime('2018-01-03 00:05:30'));
        $this->getDtCreated()->format('d-m-Y')->shouldReturn('03-01-2018');
    }

    public function it_should_save_a_dt_updated()
    {
        $this->setDtUpdated(new \DateTime('2018-01-03 00:05:30'));
        $this->getDtUpdated()->format('d-m-Y')->shouldReturn('03-01-2018');
    }

    public function it_should_save_a_category()
    {
        $category = new Category();
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);
    }

    public function it_should_save_a_author()
    {
        $author = new Utilisateur();
        $this->setAuthor($author);
        $this->getAuthor()->shouldReturn($author);
    }

    public function it_should_save_a_besoin_statut()
    {
        $besoinStatut = new BesoinStatut();
        $this->setBesoinStatut($besoinStatut);
        $this->getBesoinStatut()->shouldReturn($besoinStatut);
    }

    public function it_should_add_and_remove_sous_category()
    {
        $category = new Category();
        $sousCategory = new SousCategory();
        $sousCategory->setCategory($category);
        $this->setCategory($category);
        $this->addSousCategory($sousCategory);
        $this->getSousCategorys()->shouldContain($sousCategory);
        $this->getSousCategorys()->shouldHaveCount(1);
        $this->removeSousCategory($sousCategory);
        $this->getSousCategorys()->shouldHaveCount(0);
    }

    public function it_should_add_and_remove_answer()
    {
        $answer = new Answer();
        $this->addAnswer($answer);
        $this->getAnswers()->shouldContain($answer);
        $this->getAnswers()->shouldHaveCount(1);
        $this->removeAnswer($answer);
        $this->getAnswers()->shouldHaveCount(0);
    }

    public function it_should_save_a_company()
    {
        $company = new Company();
        $this->setCompany($company);
        $this->getCompany()->shouldReturn($company);
    }

    public function it_should_save_a_company_selected()
    {
        $companySelected = new Company();
        $this->setCompanySelected($companySelected);
        $this->getCompanySelected()->shouldReturn($companySelected);
    }

    public function it_should_save_a_comment()
    {
        $comment = new Comment();
        $this->setComment($comment);
        $this->getComment()->shouldReturn($comment);
    }
}
