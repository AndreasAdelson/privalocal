<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\Address;
use App\Domain\Model\Answer;
use App\Domain\Model\Besoin;
use App\Domain\Model\Category;
use App\Domain\Model\City;
use App\Domain\Model\Comment;
use App\Domain\Model\Company;
use App\Domain\Model\CompanyStatut;
use App\Domain\Model\CompanySubscription;
use App\Domain\Model\PaymentMethod;
use App\Domain\Model\SousCategory;
use App\Domain\Model\Utilisateur;
use PhpSpec\ObjectBehavior;

class CompanySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Company::class);
        $this->getId()->shouldReturn(null);
    }

    public function it_should_save_a_name()
    {
        $this->setName('test name');
        $this->getName()->shouldReturn('test name');
    }

    public function it_should_save_a_num_siret()
    {
        $this->setUrlName('test name');
        $this->getUrlName()->shouldReturn('test name');
    }

    public function it_should_save_an_url_name()
    {
        $this->setNumSiret('test name');
        $this->getNumSiret()->shouldReturn('test name');
    }

    public function it_should_save_category()
    {
        $category = new Category();
        $this->getCategory()->shouldReturn(null);
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);
    }

    public function it_should_save_an_user()
    {
        $utilisateur = new Utilisateur();
        $this->getUtilisateur()->shouldReturn(null);
        $this->setUtilisateur($utilisateur);
        $this->getUtilisateur()->shouldReturn($utilisateur);
    }

    public function it_should_save_a_company_statut()
    {
        $companyStatut = new CompanyStatut();
        $this->getCompanyStatut()->shouldReturn(null);
        $this->setCompanyStatut($companyStatut);
        $this->getCompanyStatut()->shouldReturn($companyStatut);
    }

    public function it_should_save_an_address()
    {
        $address = new Address();
        $this->getAddress()->shouldReturn(null);
        $this->setAddress($address);
        $this->getAddress()->shouldReturn($address);
    }

    public function it_should_save_a_city()
    {
        $city = new City();
        $this->getCity()->shouldReturn(null);
        $this->setCity($city);
        $this->getCity()->shouldReturn($city);
    }

    public function it_should_add_and_remove_company_susbcription()
    {
        $companySubscription = new CompanySubscription();
        $this->addCompanySubscription($companySubscription);
        $this->getCompanySubscriptions()->shouldContain($companySubscription);
        $this->getCompanySubscriptions()->shouldHaveCount(1);
        $this->removeCompanySubscription($companySubscription);
        $this->getCompanySubscriptions()->shouldHaveCount(0);
    }

    public function it_should_save_a_dt_created()
    {
        $this->setDtCreated(new \DateTime('2018-01-03 00:05:30'));
        $this->getDtCreated()->format('d-m-Y')->shouldReturn('03-01-2018');
    }

    public function it_should_save_a_description_full()
    {
        $this->setDescriptionFull('test description full');
        $this->getDescriptionFull()->shouldReturn('test description full');
    }

    public function it_should_save_a_description_clean()
    {
        $this->setDescriptionClean('test description clean');
        $this->getDescriptionClean()->shouldReturn('test description clean');
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

    public function it_should_add_and_remove_comment()
    {
        $comment = new Comment();
        $this->addComment($comment);
        $this->getComments()->shouldContain($comment);
        $this->getComments()->shouldHaveCount(1);
        $this->removeComment($comment);
        $this->getComments()->shouldHaveCount(0);
    }

    public function it_should_add_and_remove_author_comment()
    {
        $authorComment = new Comment();
        $this->addAuthorComment($authorComment);
        $this->getAuthorComments()->shouldContain($authorComment);
        $this->getAuthorComments()->shouldHaveCount(1);
        $this->removeAuthorComment($authorComment);
        $this->getAuthorComments()->shouldHaveCount(0);
    }

    public function it_should_add_and_remove_besoin_selected()
    {
        $besoinSelected = new Besoin();
        $this->addBesoinSelected($besoinSelected);
        $this->getBesoinSelecteds()->shouldContain($besoinSelected);
        $this->getBesoinSelecteds()->shouldHaveCount(1);
        $this->removeBesoinSelected($besoinSelected);
        $this->getBesoinSelecteds()->shouldHaveCount(0);
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

    public function it_should_save_a_image_profil()
    {
        $this->setImageProfil('test image profil');
        $this->getImageProfil()->shouldReturn('test image profil');
    }

    public function it_should_save_a_stripe_id()
    {
        $this->setStripeId('test stripe id');
        $this->getStripeId()->shouldReturn('test stripe id');
    }

    public function it_should_save_a_email()
    {
        $this->setEmail('test email');
        $this->getEmail()->shouldReturn('test email');
    }

    public function it_should_save_a_note()
    {
        $this->setNote(4.2);
        $this->getNote()->shouldReturn(4.2);
    }

    public function it_should_add_and_remove_payment_method()
    {
        $paymentMethod = new PaymentMethod();
        $this->addPaymentMethod($paymentMethod);
        $this->getPaymentMethods()->shouldContain($paymentMethod);
        $this->getPaymentMethods()->shouldHaveCount(1);
        $this->removePaymentMethod($paymentMethod);
        $this->getPaymentMethods()->shouldHaveCount(0);
    }
}
