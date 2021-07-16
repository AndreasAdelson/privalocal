<?php

namespace spec\App\Infrastructure\Repository;

use App\Domain\Model\Company;
use App\Infrastructure\Repository\CompanyRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CompanyRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em, ClassMetadata $class)
    {
        $this->beConstructedWith($em, $class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CompanyRepository::class);
    }

    /**
     * save($city) test.
     */
    public function it_should_save($em)
    {
        $city = new Company();
        $em->persist($city)->willReturn(null);
        $em->flush($city)->willReturn(null);
        $this->save($city);
        $em->persist($city)->shouldBeCalled();
        $em->flush($city)->shouldBeCalled();
    }

    /**
     * delete($city) test.
     */
    public function it_should_delete($em)
    {
        $city = new Company();
        $em->remove($city)->willReturn(null);
        $em->flush($city)->willReturn(null);
        $this->delete($city);
        $em->remove($city)->shouldBeCalled();
        $em->flush($city)->shouldBeCalled();
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

        $this->getPaginatedList('id', false, '', 'test', 1, 10, 24, 12, 1)->shouldBeLike($this->paginate($queryBuilder, 10, 1));
    }

    public function it_should_get_paginated_list_admin($em, QueryBuilder $queryBuilder, AbstractQuery $query)
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

        $this->getPaginatedListAdmin('id', false, '', 'test', 1, 10, 'VAL')->shouldBeLike($this->paginate($queryBuilder, 10, 1));
    }

    public function it_should_get_paginated_list_user($em, QueryBuilder $queryBuilder, AbstractQuery $query)
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

        $this->getPaginatedListUser('id', false, '', 'test', 1, 10, 24, 12)->shouldReturn(null);
    }

    public function it_should_get_company_by_user_id($em, QueryBuilder $queryBuilder, AbstractQuery $query)
    {
        $em->createQueryBuilder(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->select(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->from(Argument::any(), Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->where(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->andWhere(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->setParameter(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->getCompanyByUserId('id', false)->shouldReturn(null);
        $this->getCompanyByUserId('id', true)->shouldReturn(null);
    }
}
