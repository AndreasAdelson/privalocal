<?php

namespace spec\App\Infrastructure\Repository;

use App\Domain\Model\SubscriptionStatus;
use App\Infrastructure\Repository\SubscriptionStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;

class SubscriptionStatusRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em, ClassMetadata $class)
    {
        $this->beConstructedWith($em, $class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(SubscriptionStatusRepository::class);
    }

    /**
     * save($answer) test.
     */
    public function it_should_save($em)
    {
        $answer = new SubscriptionStatus();
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
        $answer = new SubscriptionStatus();
        $em->remove($answer)->willReturn(null);
        $em->flush($answer)->willReturn(null);
        $this->delete($answer);
        $em->remove($answer)->shouldBeCalled();
        $em->flush($answer)->shouldBeCalled();
    }
}
