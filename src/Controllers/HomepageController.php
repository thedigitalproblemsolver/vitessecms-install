<?php declare(strict_types=1);

namespace VitesseCms\Install\Controllers;

use VitesseCms\Content\Blocks\MainContent;
use VitesseCms\Content\Fields\Text;
use VitesseCms\Content\Fields\TextEditor;
use VitesseCms\Content\Fields\Toggle;
use VitesseCms\Install\AbstractCreatorController;

class HomepageController extends AbstractCreatorController
{
    public function createAction()
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
            ],
            'Bodytext' => [
                'calling_name' => 'bodytext',
                'type' => TextEditor::class,
                'datafieldSettings' => [
                    'multilang' => true,
                ],
            ],
            'Is Homepage' => [
                'calling_name' => 'homepage',
                'type' => Toggle::class,
                'required' => true,
            ],
        ];
        $fieldIds = $this->createDatafields($fields, 'calling_name');

        $itemGroup = $this->createDatagroup(
            'Homepage',
            'name.' . $this->configuration->getLanguageShort(),
            'views/blocks/MainContent/default_full_width',
            'content',
            $fieldIds,
            true
        );

        $this->createItems(
            [
                'Homepage' => [
                    'homepage' => [
                        'value' => '1',
                    ],
                    'bodytext' => [
                        'value' => 'Dit is de Homepage',
                        'multilang' => true,
                    ],
                ],
            ],
            'name.' . $this->configuration->getLanguageShort(),
            $itemGroup
        );

        $blocks = [
            'Bodytext' => [
                'block' => MainContent::class,
                'template' => 'views/blocks/MainContent/default_full_width',
                'position' => 'maincontent',
                'datagroup' => 'all',
            ],
        ];

        $this->createBlocks(
            $blocks,
            'name.' . $this->configuration->getLanguageShort()
        );

        $this->flash->setSucces('Hompage created');

        $this->redirect();
    }
}
