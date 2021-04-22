<?php declare(strict_types=1);

namespace VitesseCms\Install\Repositories;

use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Communication\Repositories\NewsletterRepository;
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

    public function __construct(
        DatagroupRepository $datagroupRepository,
        MigrationRepository $migrationRepository,
        BlockRepository $blockRepository
    )
    {
        $this->datagroup = $datagroupRepository;
        $this->migration = $migrationRepository;
        $this->block = $blockRepository;
    }
}
