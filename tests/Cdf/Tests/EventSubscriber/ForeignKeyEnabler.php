<?php

//declare(strict_types=1);

namespace Cdf\BiCoreBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * We enable foreign keys manually/specifically in the test environment as SQLite does not have them enabled by default.
 *
 * @see https://tomnewby.net/
 */
class ForeignKeyEnabler implements \Doctrine\Common\EventSubscriber
{
    /** @var EntityManagerInterface */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getSubscribedEvents()
    {
        return [
            'preFlush',
        ];
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        $this->manager
            ->createNativeQuery('PRAGMA foreign_keys = ON;', new ResultSetMapping())
            ->execute()
        ;
    }
}
