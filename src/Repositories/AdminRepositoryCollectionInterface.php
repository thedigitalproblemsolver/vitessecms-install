<?php declare(strict_types=1);

namespace VitesseCms\Install\Repositories;

use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Language\Repositories\LanguageRepository;

/**
 * @property ItemRepository $item;
 * @property DatagroupRepository $datagroup
 * @property DatafieldRepository $datafield
 * @property LanguageRepository $language
 */
interface AdminRepositoryCollectionInterface
{
}
