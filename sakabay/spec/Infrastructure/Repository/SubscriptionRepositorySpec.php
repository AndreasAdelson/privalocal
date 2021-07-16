<?php

namespace spec\App\Infrastructure\Repository;

use App\Domain\Model\Subscription;
use App\Infrastructure\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;

class SubscriptionRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em, ClassMetadata $class)
    {
        $this->beConstructedWith($em, $class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(SubscriptionRepository::class);
    }

    /**
     * save($answer) test.
     */
    public function it_should_save($em)
    {
        $answer = new Subscription();
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
        $answer = new Subscription();
        $em->remove($answer)->willReturn(null);
        $em->flush($answer)->willReturn(null);
        $this->delete($answer);
        $em->remove($answer)->shouldBeCalled();
        $em->flush($answer)->shouldBeCalled();
    }
}
