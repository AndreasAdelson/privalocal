<?php

namespace App\Application\Service;

use App\Domain\Model\Comment;
use App\Infrastructure\Repository\CommentRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;
use LogicException;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentService
{
    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * CommentRestController constructor.
     */
    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /// Afficher un Comment
    public function getComment(int $commentId): ?Comment
    {
        return $this->commentRepository->find($commentId);
    }

    public function getAllComments(): ?array
    {
        return $this->commentRepository->findAll();
    }

    public function deleteComment(int $commentId): void
    {
        $comment = $this->commentRepository->find($commentId);
        if (!$comment) {
            throw new EntityNotFoundException('Comment with id ' . $commentId . ' does not exist!');
        }
        $this->commentRepository->delete($comment);
    }

    /**
     * Retourne une page, potentiellement triée et filtrée.
     *
     *
     * @param string $sortBy
     * @param bool   $descending
     * @param string $filterFields
     * @param string $filterText
     * @param int    $currentPage
     * @param int    $perPage
     *
     * @return Pagerfanta
     */
    public function getPaginatedList(
        $sortBy = 'id',
        $descending = false,
        $filterFields = '',
        $filterText = '',
        $currentPage = 1,
        $perPage = PHP_INT_MAX ? PHP_INT_MAX : 10
    ) {
        return $this->commentRepository
            ->getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage);
    }

    /**
     * Retourne une page, potentiellement triée et filtrée.
     *
     *
     * @param string $sortBy
     * @param bool   $descending
     * @param string $filterFields
     * @param string $filterText
     * @param int    $currentPage
     * @param int    $perPage
     *
     * @return Pagerfanta
     */
    public function getPaginatedListUser(
        $sortBy = 'note',
        $descending = true,
        $filterFields = '',
        $filterText = '',
        $company = '',
        $note = [],
        $currentPage = 1,
        $perPage = PHP_INT_MAX ? PHP_INT_MAX : 10
    ) {
        $comments = $this->commentRepository
            ->getPaginatedListUser($sortBy, $descending, $filterFields, $filterText, $company, $note);

        return $this->paginateArray($comments, $perPage, $currentPage);
    }

    /**
     * Retourne une page en fonction d'une requète, d'une taille et d'une position.
     *

     *
     * @param int $perPage
     * @param int $currentPage
     *
     * @throws LogicException
     * @return Pagerfanta
     */
    public function paginateArray($data, $perPage, $currentPage)
    {
        $perPage = (int) $perPage;
        if (0 >= $perPage) {
            throw new \LogicException('$perPage must be greater than 0.');
        }
        if (0 >= $currentPage) {
            throw new \LogicException('$currentPage must be greater than 0.');
        }
        $pager = new Pagerfanta(new ArrayAdapter($data));
        $pager->setMaxPerPage((int) $perPage);
        $pager->setCurrentPage((int) $currentPage);

        return $pager;
    }

    public function getNbResultsByNote($company = '', $note = '')
    {
        return $this->commentRepository->getNbResultsByNote($company, $note);
    }
}
