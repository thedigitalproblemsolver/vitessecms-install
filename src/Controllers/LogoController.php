<?php declare(strict_types=1);

namespace VitesseCms\Install\Controllers;

use VitesseCms\Install\AbstractCreatorController;
use VitesseCms\Install\Forms\LogoForm;
use VitesseCms\Media\Blocks\Logo;
use VitesseCms\Setting\Enum\CallingNameEnum;
use VitesseCms\Setting\Enum\TypeEnum;

class LogoController extends AbstractCreatorController
{
    public function createAction(): void
    {
        $this->view->setVar(
            'content',
            (new LogoForm())
                ->build()
                ->renderForm('admin/install/logo/parseCreateForm')
        );
        $this->prepareView();
    }

    public function parseCreateFormAction()
    {
        $settings = [
            CallingNameEnum::LOGO_DEFAULT => [
                'type' => TypeEnum::IMAGE,
                'value' => $this->request->get('image'),
                'name' => 'Logo core',
            ],
            CallingNameEnum::LOGO_MOBILE => [
                'type' => TypeEnum::IMAGE,
                'value' => $this->request->get('image'),
                'name' => 'Logo mobile',
            ],
            CallingNameEnum::LOGO_EMAIL => [
                'type' => TypeEnum::IMAGE,
                'value' => $this->request->get('image'),
                'name' => 'Logo e-mail',
            ],
            CallingNameEnum::FAVICON => [
                'type' => TypeEnum::IMAGE,
                'value' => $this->request->get('image'),
                'name' => 'Logo favicon',
            ],
        ];
        $this->createSettings($settings);

        $blocks = [
            'Logo core' => [
                'block' => Logo::class,
                'template' => 'views/blocks/Logo/core',
                'position' => 'logo',
                'datagroup' => 'all',
            ],
            'Logo mobile' => [
                'block' => Logo::class,
                'template' => 'views/blocks/Logo/mobile',
                'position' => 'logo',
                'datagroup' => 'all',
            ],
        ];

        $this->createBlocks($blocks, 'name.' . $this->configuration->getLanguageShort());

        $this->flash->setSucces('Logo created');

        $this->redirect('admin/install/sitecreator/index');
    }
}
