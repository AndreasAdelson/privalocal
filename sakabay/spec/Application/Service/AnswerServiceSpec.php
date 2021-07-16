<?php

namespace spec\App\Application\Service;

use App\Application\Service\AnswerService;
use App\Domain\Model\Answer;
use App\Infrastructure\Repository\AnswerRepository;
use Doctrine\ORM\EntityNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AnswerServiceSpec extends ObjectBehavior
{
    public function let(
        AnswerRepository $answerRepository
    ) {
        $this->beConstructedWith($answerRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AnswerService::class);
    }

    public function it_should_get_answer($answerRepository)
    {
        $answerRepository->find(0)->willReturn(null);
        $this->getAnswer(0)->shouldReturn(null);
        $answerRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_answer($answerRepository)
    {
        $answerRepository->findAll()->willReturn([]);
        $this->getAllAnswers()->shouldReturn([]);
        $answerRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteAnswer(int $answerId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($answerRepository)
    {
        $answerRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Answer with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteAnswer', [0]);
    }

    public function it_should_delete_answer($answerRepository)
    {
        $answer = new Answer();
        $answerRepository->find(0)->willReturn($answer);
        $answerRepository->delete(Argument::any());
        $this->deleteAnswer(0);
        $answerRepository->find(0)->shouldBeCalled();
        $answerRepository->delete($answer)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($answerRepository)
    {
        $answerRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $answerRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }
}
