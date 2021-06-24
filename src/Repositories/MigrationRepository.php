<?php declare(strict_types=1);

namespace VitesseCms\Install\Repositories;

use VitesseCms\Database\Models\FindOrderIterator;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Install\Models\Migration;
use VitesseCms\Install\Models\MigrationIterator;

class MigrationRepository
{
    public function findAll(
        ?FindValueIterator $findValues = null,
        bool $hideUnpublished = true,
        ?int $limit = null,
        ?FindOrderIterator $findOrders = null
    ): MigrationIterator
    {
        Migration::setFindPublished($hideUnpublished);
        Migration::addFindOrder('name');
        if ($limit !== null) :
            Migration::setFindLimit($limit);
        endif;
        $this->parseFindValues($findValues);
        $this->parseFindOrders($findOrders);

        return new MigrationIterator(Migration::findAll());
    }

    public function findFirst(
        ?FindValueIterator $findValues = null,
        bool $hideUnpublished = true
    ): ?Migration
    {
        Migration::setFindPublished($hideUnpublished);
        $this->parsefindValues($findValues);

        /** @var Migration $migration */
        $migration = Migration::findFirst();
        if (is_object($migration)):
            return $migration;
        endif;

        return null;
    }

    protected function parseFindValues(?FindValueIterator $findValues = null): void
    {
        if ($findValues !== null) :
            while ($findValues->valid()) :
                $findValue = $findValues->current();
                Migration::setFindValue(
                    $findValue->getKey(),
                    $findValue->getValue(),
                    $findValue->getType()
                );
                $findValues->next();
            endwhile;
        endif;
    }

    protected function parseFindOrders(?FindOrderIterator $findOrders = null): void
    {
        if ($findOrders !== null) :
            while ($findOrders->valid()) :
                $findOrder = $findOrders->current();
                Migration::addFindOrder(
                    $findOrder->getKey(),
                    $findOrder->getOrder()
                );
                $findOrders->next();
            endwhile;
        endif;
    }
}