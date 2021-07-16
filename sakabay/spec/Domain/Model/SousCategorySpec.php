<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\SousCategory;
use App\Domain\Model\Besoin;
use App\Domain\Model\Category;
use App\Domain\Model\Company;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SousCategorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(SousCategory::class);
        $this->getId()->shouldReturn(null);
    }

    public function it_should_save_a_name()
    {
        $this->setName('test name');
        $this->getName()->shouldReturn('test name');
    }

    public function it_should_save_a_code()
    {
        $this->setCode('1234');
        $this->getCode()->shouldReturn('1234');
    }

    public function it_should_save_a_category()
    {
        $category = new Category();
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);
    }

    public function it_should_add_and_remove_company()
    {
        $company = new Company();
        $category = new Category();
        $company->setCategory($category);
        $this->setCategory($category);
        $this->addCompany($company);
        $this->getCompanys()->shouldContain($company);
        $this->getCompanys()->shouldHaveCount(1);
        $this->removeCompany($company);
        $this->getCompanys()->shouldHaveCount(0);
    }

    public function it_should_add_and_remove_besoin()
    {
        $besoin = new Besoin();
        $category = new Category();
        $besoin->setCategory($category);
        $this->setCategory($category);
        $this->addBesoin($besoin);
        $this->getBesoins()->shouldContain($besoin);
        $this->getBesoins()->shouldHaveCount(1);
        $this->removeBesoin($besoin);
        $this->getBesoins()->shouldHaveCount(0);
    }
}
