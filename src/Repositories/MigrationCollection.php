<?php declare(strict_types=1);

namespace VitesseCms\Install\Repositories;

use VitesseCms\Datagroup\Repositories\DatagroupRepository;

class MigrationCollection implements MigrationCollectionInterface
{
    /**
     * @var DatagroupRepository
     */
    public $datagroup;

    public function __construct(
        DatagroupRepository $datagroupRepository
    )
    {
        $this->datagroup = $datagroupRepository;
    }
}
