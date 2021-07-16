<?php

namespace spec\App\Infrastructure\Repository;

use App\Domain\Model\SousCategory;
use App\Infrastructure\Repository\SousCategoryRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SousCategoryRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em, ClassMetadata $class)
    {
        $this->beConstructedWith($em, $class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(SousCategoryRepository::class);
    }

    /**
     * save($answer) test.
     */
    public function it_should_save($em)
    {
        $answer = new SousCategory();
        $em->persist($answer)->willReturn(null);
        $em->flush($answer)->willReturn(null);
        $this->save($answer);
        $em->persist($answer)->shouldBeCalled();
        $em->flush($answer)->shouldBeCalled();
    }

    /**
     * delete($answer) test.
     */
    public function it_should_delete($em)
    {
        $answer = new SousCategory();
        $em->remove($answer)->willReturn(null);
        $em->flush($answer)->willReturn(null);
        $this->delete($answer);
        $em->remove($answer)->shouldBeCalled();
        $em->flush($answer)->shouldBeCalled();
    }

    public function it_should_get_paginated_list($em, QueryBuilder $queryBuilder, AbstractQuery $query)
    {
        $em->createQueryBuilder(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->select(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->from(Argument::any(), Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->where(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->andWhere(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->setParameter(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->orderBy(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->getPaginatedList('id', false, '', 'test', 1, 10, 1)->shouldBeLike($this->paginate($queryBuilder, 10, 1));
    }
}
