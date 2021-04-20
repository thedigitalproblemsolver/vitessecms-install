<?php declare(strict_types=1);

namespace VitesseCms\Install\Repositories;

use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Install\Models\Migration;

class MigrationRepository {

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
}