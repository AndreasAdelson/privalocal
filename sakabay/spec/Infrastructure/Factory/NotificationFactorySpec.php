<?php

namespace spec\App\Infrastructure\Factory;

use App\Domain\Model\Answer;
use App\Domain\Model\Besoin;
use App\Domain\Model\Company;
use App\Domain\Model\CompanySubscription;
use App\Domain\Model\Subscription;
use App\Domain\Model\Utilisateur;
use App\Domain\Notification\Model\Notification;
use App\Infrastructure\Factory\NotificationFactory;
use DateTime;
use Mgilet\NotificationBundle\Manager\NotificationManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationFactorySpec extends ObjectBehavior
{
    /*
     * @param \Mgilet\NotificationBundle\Manager\NotificationManager $manager
     * @param Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Swift_Mailer $mailer
     */
    public function let(
        NotificationManager $manager,
        TranslatorInterface $translator
    ) {
        $this->beConstructedWith($manager, $translator);
        $translator->trans(Argument::any())->willReturn('test');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(NotificationFactory::class);
    }

    public function it_should_validation_company_notification_admin()
    {
        $company = new Company();
        $company->setName('test');
        $this->validationCompanyNotificationAdmin([1], 'http//li.en', $company)->shouldReturn(null);
    }

    public function it_should_validation_company_notification_user()
    {
        $company = new Company();
        $company->setName('test');
        $this->validationCompanyNotificationUser([1], 'http//li.en', $company)->shouldReturn(null);
    }

    public function it_should_create_company_notification_admin()
    {
        $company = new Company();
        $company->setName('test');
        $utilisateur = new Utilisateur();
        $utilisateur->setFirstName('un');
        $utilisateur->setLastName('test');
        $company->setUtilisateur($utilisateur);
        $this->createCompanyNotificationAdmin([1], 'http//li.en', $company)->shouldReturn(null);
    }

    public function it_should_create_company_notification_user()
    {
        $company = new Company();
        $company->setName('test');
        $this->createCompanyNotificationUser([1], 'http//li.en', $company)->shouldReturn(null);
    }

    public function it_should_update_password()
    {
        $this->updatePassword([1])->shouldReturn(null);
    }

    public function it_should_create_service()
    {
        $besoin = new Besoin();
        $besoin->setTitle('test');
        $this->createService([1], 'http//li.en', $besoin)->shouldReturn(null);
    }

    public function it_should_create_answer_notification()
    {
        $answer = new Answer();
        $company = new Company();
        $besoin = new Besoin();
        $company->setName('toto');
        $besoin->setTitle('titi');
        $answer->setBesoin($besoin);
        $answer->setCompany($company);
        $this->createAnswerNotification([1], 'http//li.en', $answer)->shouldReturn(null);
    }

    public function it_should_request_quote_notification()
    {
        $answer = new Answer();
        $besoin = new Besoin();
        $besoin->setTitle('titi');
        $answer->setBesoin($besoin);
        $this->requestQuoteNotification([1], 'http//li.en', $answer)->shouldReturn(null);
    }

    public function it_should_create_company_subscription()
    {
        $companySubscription = new CompanySubscription();
        $subscription = new Subscription();
        $subscription->setName('test');
        $companySubscription->setSubscription($subscription);
        $this->createCompanySubscription([1], 'http//li.en', $companySubscription)->shouldReturn(null);
    }

    public function it_should_confirm_payment_company_subscription()
    {
        $this->confirmPaymentCompanySubscription([1], 'http//li.en')->shouldReturn(null);
    }

    public function it_should_unpaid_company_subscription()
    {
        $this->unpaidCompanySubscription([1], 'http//li.en')->shouldReturn(null);
    }

    public function it_should_cancel_company_subscription()
    {
        $companySubscription = new CompanySubscription();
        $companySubscription->setDtFin(new DateTime('2018-01-03 00:05:30'));
        $this->cancelCompanySubscription([1], 'http//li.en', $companySubscription)->shouldReturn(null);
    }

    public function it_should_trial_period_company_subscription()
    {
        $companySubscription = new CompanySubscription();
        $companySubscription->setDtFin(new DateTime('2018-01-03 00:05:30'));
        $this->trialPeriodCompanySubscription([1], 'http//li.en', $companySubscription)->shouldReturn(null);
    }
}
