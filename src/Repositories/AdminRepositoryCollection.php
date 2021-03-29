<?php declare(strict_types=1);

namespace VitesseCms\Install\Repositories;

use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Database\Interfaces\BaseRepositoriesInterface;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Language\Repositories\LanguageRepository;

class AdminRepositoryCollection implements AdminRepositoryCollectionInterface, BaseRepositoriesInterface
{
    /**
     * @var ItemRepository
     */
    public $item;

    /**
     * @var DatagroupRepository
     */
    public $datagroup;

    /**
     * @var DatafieldRepository
     */
    public $datafield;

    /**
     * @var LanguageRepository
     */
    public $language;

    public function __construct(
        ItemRepository $itemRepository,
        DatagroupRepository $datagroupRepository,
        DatafieldRepository $datafieldRepository,
        LanguageRepository $languageRepository
    )
    {
        $this->item = $itemRepository;
        $this->datagroup = $datagroupRepository;
        $this->datafield = $datafieldRepository;
        $this->language = $languageRepository;
    }
}
