<?php

namespace spec\App\Infrastructure\Repository;

use App\Domain\Model\BesoinStatut;
use App\Infrastructure\Repository\BesoinStatutRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BesoinStatutRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em, ClassMetadata $class)
    {
        $this->beConstructedWith($em, $class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(BesoinStatutRepository::class);
    }

    /**
     * save($besoin) test.
     */
    public function it_should_save($em)
    {
        $besoin = new BesoinStatut();
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
        $besoin = new BesoinStatut();
        $em->remove($besoin)->willReturn(null);
        $em->flush($besoin)->willReturn(null);
        $this->delete($besoin);
        $em->remove($besoin)->shouldBeCalled();
        $em->flush($besoin)->shouldBeCalled();
    }

    public function it_should_get_besoin_statut_without_pub($em, QueryBuilder $queryBuilder, AbstractQuery $query)
    {
        $em->createQueryBuilder(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->select(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->from(Argument::any(), Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->where(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->orderBy(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->setParameter(Argument::any(), Argument::any())->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->getBesoinStatutsWithoutPUB()
            ->shouldReturn(null);
    }
}
