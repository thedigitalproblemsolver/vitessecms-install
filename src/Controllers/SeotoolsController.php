<?php declare(strict_types=1);

namespace VitesseCms\Install\Controllers;

use VitesseCms\Install\AbstractCreatorController;
use VitesseCms\Install\Forms\SeotoolsForm;
use VitesseCms\Setting\Enum\CallingNameEnum;
use VitesseCms\Setting\Enum\TypeEnum;

class SeotoolsController extends AbstractCreatorController
{
    public function createAction(): void
    {
        $this->view->setVar(
            'content',
            (new SeotoolsForm())
                ->build()
                ->renderForm('admin/install/seotools/parseCreateForm')
        );
        $this->prepareView();
    }

    public function parseCreateFormAction()
    {
        $settings = [];

        if (!$this->setting->has(CallingNameEnum::GOOGLE_ANALYTICS_TRACKINGID,false)) :
            $settings[CallingNameEnum::GOOGLE_ANALYTICS_TRACKINGID] = [
                'type' => TypeEnum::TEXT,
                'value' => $this->request->get('ga_tracking_id'),
                'name' => 'Google Analytics tracking ID',
            ];
        endif;

        if (!$this->setting->has(CallingNameEnum::GOOGLE_SITE_VERIFICATION, false)) :
            $settings[CallingNameEnum::GOOGLE_SITE_VERIFICATION] = [
                'type' => TypeEnum::TEXT,
                'value' => $this->request->get('google_site_verification'),
                'name' => 'Google Site Verification',
            ];
        endif;

        $this->createSettings($settings);

        $this->flash->setSucces('Seotools created');

        $this->redirect('admin/install/sitecreator/index');
    }
}
