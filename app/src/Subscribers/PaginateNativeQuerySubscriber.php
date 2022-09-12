<?php

declare(strict_types=1);

namespace App\Subscribers;

use Doctrine\ORM\NativeQuery;
use Knp\Component\Pager\Event\ItemsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaginateNativeQuerySubscriber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event): void
    {
        if (!($event->target instanceof NativeQuery)) {
            return;
        }

        $event->count = (isset($event->options['count'])) ? $event->options['count'] : count($event->target->getResult());

        $sqlQuery = $event->target->getSQL();
        $sqlQuery .= " OFFSET {$event->getOffset()} LIMIT {$event->getLimit()}";
        $event->target->setSQL($sqlQuery);

        $event->items = $event->target->getResult();

        $event->stopPropagation();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'knp_pager.items' => ['items', 1]
        ];
    }
}
