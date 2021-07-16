<?php

namespace spec\App\Infrastructure\Repository;

use App\Domain\Model\Advantage;
use App\Infrastructure\Repository\AdvantageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;

class AdvantageRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em, ClassMetadata $class)
    {
        $this->beConstructedWith($em, $class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AdvantageRepository::class);
    }

    /**
     * save($advantage) test.
     */
    public function it_should_save($em)
    {
        $advantage = new Advantage();
        $em->persist($advantage)->willReturn(null);
        $em->flush($advantage)->willReturn(null);
        $this->save($advantage);
        $em->persist($advantage)->shouldBeCalled();
        $em->flush($advantage)->shouldBeCalled();
    }

    /**
     * delete($advantage) test.
     */
    public function it_should_delete($em)
    {
        $advantage = new Advantage();
        $em->remove($advantage)->willReturn(null);
        $em->flush($advantage)->willReturn(null);
        $this->delete($advantage);
        $em->remove($advantage)->shouldBeCalled();
        $em->flush($advantage)->shouldBeCalled();
    }
}
