<?php declare(strict_types=1);

namespace VitesseCms\Install\Repositories;

use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;

/**
 * @property DatagroupRepository $datagroup
 * @property MigrationRepository $migration
 * @property BlockRepository $block
 * @property DatafieldRepository $datafield
 */
interface MigrationCollectionInterface
{
}
