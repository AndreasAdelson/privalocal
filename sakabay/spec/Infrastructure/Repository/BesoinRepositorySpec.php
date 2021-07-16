<?php

namespace spec\App\Infrastructure\Repository;

use App\Domain\Model\Besoin;
use App\Infrastructure\Repository\BesoinRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Base;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BesoinRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em, ClassMetadata $class)
    {
        $this->beConstructedWith($em, $class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(BesoinRepository::class);
    }

    /**
     * save($besoin) test.
     */
    public function it_should_save($em)
    {
        $besoin = new Besoin();
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
        $besoin = new Besoin();
        $em->remove($besoin)->willReturn(null);
        $em->flush($besoin)->willReturn(null);
        $this->delete($besoin);
        $em->remove($besoin)->shouldBeCalled();
        $em->flush($besoin)->shouldBeCalled();
    }

    public function it_should_get_paginated_besoin_by_user_id($em, QueryBuilder $queryBuilder, AbstractQuery $query,  Expr $expr, Base $base)
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

        $this->getPaginatedBesoinByUserId('1', ['VAL'], '4', 'false')
            ->shouldReturn(null);

        $this->getPaginatedBesoinByUserId('1', 'VAL', null, 'true')
            ->shouldReturn(null);
    }

    public function it_should_get_besoin_answered_by_company($em, QueryBuilder $queryBuilder, AbstractQuery $query)
    {
        $em->createQueryBuilder(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->select(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->from(Argument::any(), Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->where(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->andWhere(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->setParameters(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->addSelect(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->getBesoinAnsweredByCompany('1', '2')->shouldReturn(null);
    }

    public function it_should_get_paginated_opportunity_list($em, QueryBuilder $queryBuilder, AbstractQuery $query,  Expr $expr, Base $base)
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

        $this->getPaginatedOpportunityList('1', ['VAL'], 'false', 'false')
            ->shouldReturn(null);

        $this->getPaginatedOpportunityList('1', 'VAL', 'true', 'true')
            ->shouldReturn(null);
    }

    public function it_should_get_paginated_opportunity_with_requested_quote_list($em, QueryBuilder $queryBuilder, AbstractQuery $query)
    {
        $em->createQueryBuilder(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->select(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->from(Argument::any(), Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->orWhere(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->where(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->andWhere(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->setParameter(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->orderBy(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->addSelect(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->getPaginatedOpportunityWithRequestedQuoteList('1', false, false)
            ->shouldReturn(null);

        $this->getPaginatedOpportunityWithRequestedQuoteList('1', true, true)
            ->shouldReturn(null);
    }
}
