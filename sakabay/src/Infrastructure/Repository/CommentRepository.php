<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class CommentRepository extends AbstractRepository implements CommentRepositoryInterface
{

    /**
     * CommentRepository constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(Comment::class));
    }

    public function save(Comment $comment): void
    {
        $this->_em->persist($comment);
        $this->_em->flush($comment);
    }

    public function delete(Comment $comment): void
    {
        $this->_em->remove($comment);
        $this->_em->flush($comment);
    }

    public function getAverageNotation($companyId = '')
    {
        $qb = $this->createQueryBuilder('c');
        if (!empty($companyId)) {
            $qb->select('avg(c.note) as note_avg, count(c.note) as note_count')
                ->where('c.company = :companyId')
                ->groupBy('c.company')
                ->setParameter('companyId', $companyId);
        }
        return $qb->getQuery()->getResult();
    }

    public function getPaginatedListUser(
        $sortBy = 'note',
        $descending = true,
        $filterFields = '',
        $filterText = '',
        $company = '',
        $notes = []
    ) {
        $qb = $this->createQueryBuilder('c');
        if (!empty($filterText)) {
            $queryFilter = ' LOWER(c.'
                . implode(') LIKE LOWER(:searchPattern) OR LOWER(c.', explode(',', $filterFields))
                . ') LIKE LOWER(:searchPattern)';
            $qb->andWhere($queryFilter);
            $qb->setParameter('searchPattern', '%' . mb_strtolower($filterText) . '%');
        }

        if (!empty($company)) {
            $qb->leftJoin('c.company', 'company')
                ->andWhere('company.id = :companyId')
                ->setParameter('companyId', $company);
        }

        if (!empty($notes) && is_array($notes)) {
            $orStatements = $qb->expr()->orX();
            foreach ($notes as $note) {
                $orStatements->add(
                    $qb->expr()->like('c.note', $qb->expr()->literal('%' . $note . '%'))
                );
            }
            $qb->andWhere($orStatements);
        }


        if (!empty($sortBy)) {
            $qb->orderBy('c.' . $sortBy, $descending ? 'DESC' : 'ASC');
        }
        return $qb->getQuery()->getResult();
    }

    public function getNbResultsByNote($company = '', $note = '')
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('count(DISTINCT c.id)');
        if (!empty($company)) {
            $qb->leftJoin('c.company', 'company')
                ->andWhere('company.id = :companyId')
                ->setParameter('companyId', $company);
        }

        if (!empty($note)) {
            $qb->andWhere($qb->expr()->eq('c.note', $note));
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
