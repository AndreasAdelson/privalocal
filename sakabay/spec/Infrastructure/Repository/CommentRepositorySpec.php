<?php

namespace spec\App\Infrastructure\Repository;

use App\Domain\Model\Comment;
use App\Infrastructure\Repository\CommentRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Base;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CommentRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em, ClassMetadata $class)
    {
        $this->beConstructedWith($em, $class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CommentRepository::class);
    }

    /**
     * save($besoin) test.
     */
    public function it_should_save($em)
    {
        $besoin = new Comment();
        $em->persist($besoin)->willReturn(null);
        $em->flush($besoin)->willReturn(null);
        $this->save($besoin);
        $em->persist($besoin)->shouldBeCalled();
        $em->flush($besoin)->shouldBeCalled();
    }

    /**
     * delete($besoin) test.
     */
    public function it_should_delete($em)
    {
        $besoin = new Comment();
        $em->remove($besoin)->willReturn(null);
        $em->flush($besoin)->willReturn(null);
        $this->delete($besoin);
        $em->remove($besoin)->shouldBeCalled();
        $em->flush($besoin)->shouldBeCalled();
    }

    public function it_should_get_average_notation($em, QueryBuilder $queryBuilder, AbstractQuery $query)
    {
        $em->createQueryBuilder(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->select(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->from(Argument::any(), Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->where(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->groupBy(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->setParameter(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->getAverageNotation('1')->shouldReturn(null);
    }

    public function it_should_get_paginated_list_user($em, QueryBuilder $queryBuilder, AbstractQuery $query,  Expr $expr, Base $base)
    {
        $em->createQueryBuilder(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->select(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->from(Argument::any(), Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->expr()->willReturn($expr);
        $expr->orX()->willReturn($base);
        $base->add(Argument::any())->shouldBeCalled();
        $expr->like(Argument::any(), Argument::any())->shouldBeCalled();
        $expr->literal(Argument::any())->shouldBeCalled();
        $queryBuilder->orWhere(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->where(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->andWhere(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->setParameter(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->orderBy(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->getPaginatedListUser('note', true, '', 'test', 'id', [1, 2])->shouldReturn(null);

        $this->getPaginatedListUser('note', false, '', '', 'id', [])->shouldReturn(null);
    }

    public function it_should_get_nb_result_by_note($em, QueryBuilder $queryBuilder, AbstractQuery $query, Expr $expr, Base $base)
    {
        $em->createQueryBuilder(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->select(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->from(Argument::any(), Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->expr()->willReturn($expr);
        $expr->eq(Argument::any(), Argument::any(), Argument::any())->willReturn($base);
        $queryBuilder->andWhere(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->setParameter(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->addSelect(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->getNbResultsByNote('1', '2')->shouldReturn(null);
    }
}
