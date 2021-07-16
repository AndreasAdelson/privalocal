<?php

namespace spec\App\Application\Service;

use App\Application\Service\CommentService;
use App\Domain\Model\Comment;
use App\Infrastructure\Repository\CommentRepository;
use Doctrine\ORM\EntityNotFoundException;
use LogicException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CommentServiceSpec extends ObjectBehavior
{
    public function let(
        CommentRepository $commentRepository
    ) {
        $this->beConstructedWith($commentRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CommentService::class);
    }

    public function it_should_get_comment($commentRepository)
    {
        $commentRepository->find(0)->willReturn(null);
        $this->getComment(0)->shouldReturn(null);
        $commentRepository->find(0)->shouldBeCalled();
    }

    public function it_should_get_all_comment($commentRepository)
    {
        $commentRepository->findAll()->willReturn([]);
        $this->getAllComments()->shouldReturn([]);
        $commentRepository->findAll()->shouldBeCalled();
    }

    /**
     * deleteComment(int $commentId) test
     * when findById returns null.
     */
    public function it_should_throw_error_when_trying_to_delete_nonexistent_object($commentRepository)
    {
        $commentRepository->find(0)->willReturn(null);
        $expectedException = new EntityNotFoundException('Comment with id 0 does not exist!');
        $this->shouldThrow($expectedException)->during('deleteComment', [0]);
    }

    public function it_should_delete_comment($commentRepository)
    {
        $comment = new Comment();
        $commentRepository->find(0)->willReturn($comment);
        $commentRepository->delete(Argument::any());
        $this->deleteComment(0);
        $commentRepository->find(0)->shouldBeCalled();
        $commentRepository->delete($comment)->shouldBeCalled();
    }

    /**
     * getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage) test.
     */
    public function it_should_get_paginated_list($commentRepository)
    {
        $commentRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldReturn([0]);
        $commentRepository->getPaginatedList(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }

    public function it_should_get_paginated_list_user($commentRepository)
    {
        $commentRepository->getPaginatedListUser(1, 2, 3, 4, 5, 6)->willReturn([0]);
        $this->getPaginatedListUser(1, 2, 3, 4, 5, 6, 1, 10);
        $commentRepository->getPaginatedListUser(1, 2, 3, 4, 5, 6)->shouldBeCalled();
    }

    public function it_should_paginate_array()
    {
        $expectedException = new LogicException('$perPage must be greater than 0.');
        $this->shouldThrow($expectedException)->during('paginateArray', [[], 0, 1]);
        $expectedException = new LogicException('$currentPage must be greater than 0.');
        $this->shouldThrow($expectedException)->during('paginateArray', [[], 1, 0]);
    }

    public function it_should_get_nb_result_by_note($commentRepository)
    {
        $commentRepository->getNbResultsByNote(1, 2)->willReturn([0]);
        $this->getNbResultsByNote(1, 2);
        $commentRepository->getNbResultsByNote(1, 2)->shouldBeCalled();
    }
}
