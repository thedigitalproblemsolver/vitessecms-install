<?php declare(strict_types=1);

namespace VitesseCms\Install\Repositories;

use VitesseCms\Datagroup\Repositories\DatagroupRepository;

class MigrationCollection implements MigrationCollectionInterface
{
    /**
     * @var DatagroupRepository
     */
    public $datagroup;

    /**
     * @var MigrationRepository
     */
    public $migration;

    public function __construct(
        DatagroupRepository $datagroupRepository,
        MigrationRepository $migrationRepository
    )
    {
        $this->datagroup = $datagroupRepository;
        $this->migration = $migrationRepository;
    }
}
