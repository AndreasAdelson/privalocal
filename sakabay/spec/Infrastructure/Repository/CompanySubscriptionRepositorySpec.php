<?php

namespace spec\App\Infrastructure\Repository;

use App\Domain\Model\CompanySubscription;
use App\Infrastructure\Repository\CompanySubscriptionRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CompanySubscriptionRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em, ClassMetadata $class)
    {
        $this->beConstructedWith($em, $class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CompanySubscriptionRepository::class);
    }

    /**
     * save($answer) test.
     */
    public function it_should_save($em)
    {
        $answer = new CompanySubscription();
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
        $answer = new CompanySubscription();
        $em->remove($answer)->willReturn(null);
        $em->flush($answer)->willReturn(null);
        $this->delete($answer);
        $em->remove($answer)->shouldBeCalled();
        $em->flush($answer)->shouldBeCalled();
    }

    public function it_should_get_active_subscription($em, QueryBuilder $queryBuilder, AbstractQuery $query)
    {
        $em->createQueryBuilder(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->select(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->from(Argument::any(), Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->leftJoin(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->where(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->andWhere(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->setParameters(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->setParameter(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->getActiveSubscription('id', false, false)->shouldReturn(null);
        $this->getActiveSubscription('id', true, true)->shouldReturn(null);
        $this->getActiveSubscription('', true, true)->shouldReturn([]);

    }
}
