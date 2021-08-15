<?php declare(strict_types=1);

namespace VitesseCms\Install\Controllers;

use VitesseCms\Content\Fields\Model;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Install\Repositories\AdminRepositoriesInterface;
use VitesseCms\Setting\Factory\SettingFactory;
use VitesseCms\Install\AbstractCreatorController;
use VitesseCms\Install\Forms\AffiliateForm;
use VitesseCms\Shop\Blocks\AffiliateInitialize;
use VitesseCms\Shop\Blocks\AffiliateOrderOverview;
use VitesseCms\User\Models\User;

class AffiliateController extends AbstractCreatorController implements AdminRepositoriesInterface
{
    public function createAction(): void
    {
        if (!$this->setting->has('SHOP_DATAGROUP_AFFILIATE')) :
            $this->view->setVar('content',
                (new AffiliateForm())
                    ->setRepositories($this->repositories)
                    ->buildForm()
                    ->renderForm('admin/install/affiliate/parseCreateForm')
            );
            $this->prepareView();
        else :
            parent::redirect('admin/install/affiliate/doCreate');
        endif;
    }

    public function parseCreateFormAction(): void
    {
        if ((new AffiliateForm())->validate($this)) :
            if (!$this->setting->has('SHOP_DATAGROUP_AFFILIATE')) :
                SettingFactory::create(
                    'SHOP_DATAGROUP_AFFILIATE',
                    'SettingDatagroup',
                    $this->request->getPost('SHOP_DATAGROUP_AFFILIATE'),
                    'SHOP_DATAGROUP_AFFILIATE',
                    true
                )->save();
            endif;
            parent::redirect('admin/install/affiliate/doCreate');
        else :
            $this->flash->setError('Error in form');
            parent::redirect('admin/install/sitecreator/index');
        endif;
    }

    public function doCreateAction(): void
    {
        $datagroup = $this->createContent();
        $this->createAffiliateBlocks($datagroup);

        $this->flash->setSucces('Affiliate is activated');
        parent::redirect('admin/install/sitecreator/index');
    }

    protected function createContent(): Datagroup
    {
        $fields = [
            'Affiliate gebruiker' => [
                'calling_name' => 'affiliateUser',
                'type' => Model::class,
                'datafieldSettings' => [
                    'inputType' => 'text',
                    'useSelect2' => true,
                    'displayLimit' => 999,
                    'model' => User::class,
                ],
                'required' => true,
            ],
        ];
        $fieldIds = $this->createDatafields($fields, 'calling_name');

        /** @var Datagroup $datagroup */
        $datagroup = Datagroup::findById($this->setting->get('SHOP_DATAGROUP_AFFILIATE'));
        foreach ($fieldIds as $fieldId) :
            /** @var Datafield $datafield */
            $datafield = Datafield::findById($fieldId['id']);
            $datagroup->addDatafield($datafield);
        endforeach;
        $datagroup->save();

        return $datagroup;
    }

    protected function createAffiliateBlocks(Datagroup $datagroup): void
    {
        $blocks = [
            'Mijn wederverkopen' => [
                'block' => AffiliateOrderOverview::class,
                'template' => 'views/blocks/AffiliateOrderOverview/core',
                'position' => 'myaccount',
                'datagroup' => [],
            ],
            'Affiliate initializer' => [
                'block' => AffiliateInitialize::class,
                'template' => 'views/blocks/AffiliateInitialize/core',
                'position' => 'footer',
                'datagroup' => [(string)$datagroup->getId()],
                'blockSettings' => [
                    'datagroups' => ['value' => [(string)$datagroup->getId()]],
                    'cookieLifetime' => ['value' => 30],
                ],
            ],
        ];
        $this->createBlocks(
            $blocks,
            'name.' . $this->configuration->getLanguageShort()
        );
    }
}
