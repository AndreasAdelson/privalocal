<?php

namespace spec\App\Domain\Model;

use App\Domain\Model\Answer;
use App\Domain\Model\Besoin;
use App\Domain\Model\Company;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AnswerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Answer::class);
        $this->getId()->shouldReturn(null);
    }

    public function it_should_save_a_message()
    {
        $this->setMessage('test name');
        $this->getMessage()->shouldReturn('test name');
    }

    public function it_should_save_a_dt_created()
    {
        $this->setDtCreated(new \DateTime('2018-01-03 00:05:30'));
        $this->getDtCreated()->format('d-m-Y')->shouldReturn('03-01-2018');
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

    public function it_should_save_a_request_quote()
    {
        $this->setRequestQuote(true);
        $this->getRequestQuote()->shouldReturn(true);
    }

    public function it_should_save_a_quote()
    {
        $this->setQuote('test name');
        $this->getQuote()->shouldReturn('test name');
    }

    public function it_should_save_a_message_email()
    {
        $this->setMessageEmail('test name');
        $this->getMessageEmail()->shouldReturn('test name');
    }

    public function it_should_save_a_file()
    {
        $file = new UploadedFile(
            __DIR__ . '/../../fixtures/logo.png',
            'logo.png'
        );
        $this->setFile($file);
        $this->getFile()->shouldReturn($file);
    }
}
