<?php

declare(strict_types=1);

namespace VitesseCms\Install;

use VitesseCms\Block\Factories\BlockFactory;
use VitesseCms\Block\Factories\BlockPositionFactory;
use VitesseCms\Block\Models\Block;
use VitesseCms\Communication\Factories\EmailFactory;
use VitesseCms\Communication\Models\Email;
use VitesseCms\Content\Controllers\AdminitemController;
use VitesseCms\Content\Enum\ItemEnum;
use VitesseCms\Content\Factories\ItemFactory;
use VitesseCms\Content\Fields\Text;
use VitesseCms\Content\Fields\TextEditor;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\AbstractController;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Datafield\Factories\DatafieldFactory;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Datagroup\Enums\DatagroupEnum;
use VitesseCms\Datagroup\Factories\DatagroupFactory;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Install\Interfaces\AdminRepositoriesInterface;
use VitesseCms\Setting\Factory\SettingFactory;
use VitesseCms\Setting\Models\Setting;
use VitesseCms\User\Enum\UserRoleEnum;
use VitesseCms\User\Factories\PermissionRoleFactory;
use VitesseCms\User\Models\PermissionRole;
use VitesseCms\User\Utils\PermissionUtils;

abstract class AbstractCreatorController extends AbstractController implements AdminRepositoriesInterface
{
    private array $settingsRepository;
    private ItemRepository $itemRepository;
    private DatagroupRepository $datagroupRepository;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->settingsRepository = [];
        $this->itemRepository = $this->eventsManager->fire(ItemEnum::GET_REPOSITORY, new \stdClass());
        $this->datagroupRepository = $this->eventsManager->fire(DatagroupEnum::GET_REPOSITORY->value, new \stdClass());
    }

    public function initialize()
    {
        if (!PermissionUtils::check(
            $this->user,
            $this->router->getModulePrefix().$this->router->getModuleName(),
            $this->router->getControllerName(),
            $this->router->getActionName()
        )) {
            $this->flash->setError('USER_NO_ACCESS');
            $this->response->redirect($this->url->getBaseUri());

            $this->disableView();
        }
    }

    public function createBasicPermissionRoles(): void
    {
        $roles = [
            'SuperAdmin' => [
                'calling_name' => UserRoleEnum::SUPER_ADMIN,
                'adminAccess' => true,
            ],
            'Admin' => [
                'calling_name' => 'admin',
                'adminAccess' => true,
            ],
            'Geregistreerd' => [
                'calling_name' => 'registered',
            ],
            'Gast' => [
                'calling_name' => 'guest',
            ],
        ];

        $this->createPermissionRoles($roles);
    }

    protected function createPermissionRoles(array $roles): void
    {
        foreach ($roles as $name => $params) {
            PermissionRole::setFindValue('calling_name', $params['calling_name']);
            PermissionRole::setFindPublished(false);
            $permissionRoles = PermissionRole::findAll();
            if (0 === \count($permissionRoles)) {
                PermissionRoleFactory::create(
                    $name,
                    $params['calling_name'],
                    true,
                    isset($params['adminAccess']) ? true : false,
                    isset($params['parentId']) ? $params['parentId'] : null
                )->save();
            }
        }
    }

    protected function createItems(
        array $pages,
        string $titleField,
        Datagroup $datagroup,
        string $parentId = null,
        int $startOrder = 0
    ): array {
        $adminitemController = new AdminitemController();

        $return = [
            'pages' => [],
            'ids' => [],
        ];
        foreach ($pages as $title => $params) {
            $item = $this->itemRepository->findFirst(
                new FindValueIterator([
                    new FindValue($titleField, $title),
                    new FindValue('datagroup', (string) $datagroup->getId()),
                ]),
                false
            );
            if (null === $item) {
                $fieldValues = [];
                if (is_array($params)) {
                    $fieldValues = $params;
                }
                $item = ItemFactory::create(
                    $title,
                    (string) $datagroup->getId(),
                    $fieldValues,
                    true,
                    $parentId,
                    $startOrder
                );
                $this->eventsManager->fire(
                    AdminitemController::class.':beforeModelSave',
                    $adminitemController,
                    $item
                );
                $item->save();
            }
            $return['pages'][$title] = (string) $item->getId();
            $return['ids'][] = (string) $item->getId();
            ++$startOrder;
        }

        return $return;
    }

    protected function createBlocks(
        array $blocks,
        string $titleField,
        int $order = 10
    ): array {
        $return = [
            'blocks' => [],
            'ids' => [],
        ];
        foreach ($blocks as $title => $params) {
            Block::setFindValue($titleField, $title);
            Block::setFindPublished(false);
            $blocks = Block::findAll();
            if (0 === \count($blocks)) {
                $blockSettings = [];
                if (isset($params['blockSettings'])) {
                    $blockSettings = $params['blockSettings'];
                }

                $block = BlockFactory::create(
                    $title,
                    $params['block'],
                    $params['template'],
                    $blockSettings,
                    true,
                    $order
                );
                $block->save();

                if (isset($params['position']) && isset($params['datagroup'])) {
                    BlockPositionFactory::create(
                        ucfirst($params['position']).' - '.$block->_('name'),
                        (string) $block->getId(),
                        $params['position'],
                        $params['datagroup'],
                        true,
                        $order
                    )->save();
                }
            } else {
                $block = $blocks[0];
            }
            $return['blocks'][$title] = (string) $block->getId();
            $return['ids'][] = (string) $block->getId();
            ++$order;
        }

        return $return;
    }

    protected function createContentDatagroup(array $extraFields = null): Datagroup
    {
        $fields = [
            'Item naam' => [
                'calling_name' => 'name',
                'type' => Text::class,
                'datafieldSettings' => [
                    'inputType' => 'text',
                    'multilang' => true,
                ],
                'required' => true,
                'slug' => true,
            ],
            'Introtext' => [
                'calling_name' => 'introtext',
                'type' => TextEditor::class,
                'datafieldSettings' => [
                    'multilang' => true,
                ],
            ],
            'Bodytext' => [
                'calling_name' => 'bodytext',
                'type' => TextEditor::class,
                'datafieldSettings' => [
                    'multilang' => true,
                ],
            ],
        ];
        if (is_array($extraFields)) {
            $fields = array_merge($fields, $extraFields);
        }

        $fieldIds = $this->createDatafields($fields, 'calling_name');

        return $this->createDatagroup(
            'Pagina',
            'name.'.$this->configuration->getLanguageShort(),
            'views/blocks/MainContent/core',
            'content',
            $fieldIds,
            true
        );
    }

    protected function createDatafields(
        array $fields,
        string $titleField,
        int $order = 10
    ): array {
        $return = [];
        foreach ($fields as $title => $params) {
            Datafield::setFindValue($titleField, $params['calling_name']);
            Datafield::setFindPublished(false);
            $datafields = Datafield::findAll();
            if (0 === \count($datafields)) {
                $datafieldSettings = [];
                if (isset($params['datafieldSettings'])) {
                    $datafieldSettings = $params['datafieldSettings'];
                }

                $datafield = DatafieldFactory::create(
                    $title,
                    $params['calling_name'],
                    $params['type'],
                    $datafieldSettings,
                    true,
                    $order
                );
                $datafield->save();
            } else {
                $datafield = $datafields[0];
            }

            $return[] = [
                'id' => (string) $datafield->getId(),
                'published' => true,
                'required' => isset($params['required']) ? true : false,
                'slug' => isset($params['slug']) ? true : false,
                'slugCategory' => isset($params['slugCategory']) ? true : false,
                'seoTitle' => isset($params['seoTitle']) ? true : false,
            ];
            ++$order;
        }

        return $return;
    }

    protected function createDatagroup(
        string $title,
        string $titleField,
        string $template,
        string $component,
        array $datafields = [],
        bool $includeInSitemap = false,
        string $parentId = null,
        string $itemOrdering = ''
    ): Datagroup {
        $datagroup = $this->datagroupRepository->findFirst(
            new FindValueIterator([
                new FindValue($titleField, $title),
                new FindValue('component', $component),
            ]),
            false
        );

        $slugDatafields = [];
        $slugCategoryDatafields = [];
        $seoTitleDatafields = [];
        if (null === $datagroup) {
            $datagroup = DatagroupFactory::create(
                $title,
                $template,
                $component,
                $datafields,
                true,
                $includeInSitemap,
                $parentId,
                $itemOrdering
            );
        } else {
            $slugDatafields = $datagroup->getSlugDatafields();
            $slugCategoryDatafields = $datagroup->getSlugCategories();
            $seoTitleDatafields = $datagroup->getSeoTitleDatafields();
            $datagroup->set('datafields', $datafields);
        }

        /** @var Datafield $datafield */
        foreach ($datafields as $datafield) {
            if (isset($datafield['slug']) && $datafield['slug']) {
                $slugDatafields[$datafield['id']] = new \stdClass();
                $slugDatafields[$datafield['id']]->id = $datafield['id'];
                $slugDatafields[$datafield['id']]->published = true;
            }
            if (isset($datafield['slugCategory']) && $datafield['slugCategory'] && null !== $parentId) {
                $slugCategoryDatafields[$parentId] = new \stdClass();
                $slugCategoryDatafields[$parentId]->id = $datafield['id'];
                $slugCategoryDatafields[$parentId]->published = true;
            }
            if (isset($datafield['seoTitle']) && $datafield['seoTitle']) {
                $seoTitleDatafields[$datafield['id']] = new \stdClass();
                $seoTitleDatafields[$datafield['id']]->id = $datafield['id'];
                $seoTitleDatafields[$datafield['id']]->published = true;
            }
        }

        $datagroup->setSlugDatafields($slugDatafields);
        $datagroup->setSlugCategories($slugCategoryDatafields);
        $datagroup->setSeoTitleDatafields($seoTitleDatafields);
        $datagroup->save();

        return $datagroup;
    }

    protected function createSystemEmails(
        array $emails,
        string $subjectField
    ): array {
        $return = [
            'emails' => [],
            'ids' => [],
        ];
        foreach ($emails as $subject => $params) {
            Email::setFindValue($subjectField, $subject);
            Email::setFindPublished(false);
            $emails = Email::findAll();
            if (0 === \count($emails)) {
                $email = EmailFactory::create(
                    $subject,
                    $params['body'],
                    $params['systemAction'],
                    $params['triggerState'],
                    isset($params['published']) ? $params['published'] : false,
                    isset($params['alternativeRecipient']) ? $params['alternativeRecipient'] : null,
                    isset($params['messageSuccess']) ? $params['messageSuccess'] : null,
                    isset($params['messageError']) ? $params['messageError'] : null
                );
                $email->save();
            } else {
                $email = $emails[0];
            }
            $return['emails'][$subject] = (string) $email->getId();
            $return['ids'][] = (string) $email->getId();
        }

        $this->createSettings([
            'WEBSITE_CONTACT_EMAIL' => ['value' => ''],
            'WEBSITE_DEFAULT_NAME' => ['value' => ''],
        ]);

        return $return;
    }

    protected function createSettings(array $settings): void
    {
        foreach ($settings as $settingKey => $params) {
            Setting::setFindValue('calling_name', $settingKey);
            Setting::setFindPublished(false);
            $settingItems = Setting::findAll();
            if (0 === \count($settingItems)) {
                $setting = SettingFactory::create(
                    $settingKey,
                    isset($params['type']) ? $params['type'] : 'SettingText',
                    isset($params['value']) ? $params['value'] : '',
                    isset($params['name']) ? $params['name'] : '',
                    true
                );
                $setting->save();
            }
        }
    }
}
