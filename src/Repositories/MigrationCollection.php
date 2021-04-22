<?php declare(strict_types=1);

namespace VitesseCms\Install\Repositories;

use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
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

    /**
     * @var BlockRepository
     */
    public $block;

    /**
     * @var DatafieldRepository
     */
    public $datafield;

    public function __construct(
        DatagroupRepository $datagroupRepository,
        MigrationRepository $migrationRepository,
        BlockRepository $blockRepository,
        DatafieldRepository $datafieldRepository
    )
    {
        $this->datagroup = $datagroupRepository;
        $this->migration = $migrationRepository;
        $this->block = $blockRepository;
        $this->datafield = $datafieldRepository;
    }
}
