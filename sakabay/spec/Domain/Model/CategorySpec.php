<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\Besoin;
use App\Domain\Model\Category;
use App\Domain\Model\Company;
use App\Domain\Model\SousCategory;
use PhpSpec\ObjectBehavior;

class CategorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Category::class);
        $this->getId()->shouldReturn(null);
    }

    public function it_should_save_a_name()
    {
        $this->setName('test name');
        $this->getName()->shouldReturn('test name');
    }

    public function it_should_save_a_code()
    {
        $this->setCode('SKB');
        $this->getCode()->shouldReturn('SKB');
    }

    public function it_should_save_company()
    {
        $company = new Company();
        $this->addCompany($company);
        $this->getCompanys()->shouldContain($company);
        $this->getCompanys()->shouldHaveCount(1);
        $this->removeCompany($company);
        $this->getCompanys()->shouldHaveCount(0);
    }

    public function it_should_save_sous_category()
    {
        $sousCategory = new SousCategory();
        $this->addSousCategory($sousCategory);
        $this->getSousCategorys()->shouldContain($sousCategory);
        $this->getSousCategorys()->shouldHaveCount(1);
        $this->removeSousCategory($sousCategory);
        $this->getSousCategorys()->shouldHaveCount(0);
    }

    public function it_should_save_besoin()
    {
        $besoin = new Besoin();
        $this->addBesoin($besoin);
        $this->getBesoins()->shouldContain($besoin);
        $this->getBesoins()->shouldHaveCount(1);
        $this->removeBesoin($besoin);
        $this->getBesoins()->shouldHaveCount(0);
    }
}
