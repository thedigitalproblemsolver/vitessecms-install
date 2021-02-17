<?php declare(strict_types=1);

namespace VitesseCms\Install\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;

class ContactForm extends AbstractForm
{

    public function initialize()
    {
        $this->addText(
            'Website name',
            'WEBSITE_DEFAULT_NAME',
            (new Attributes())
                ->setRequired()
                ->setReadonly($this->setting->has('WEBSITE_DEFAULT_NAME'))
                ->setDefaultValue($this->setting->get('WEBSITE_DEFAULT_NAME'))
        )->addEmail(
            'Website email',
            'WEBSITE_CONTACT_EMAIL',
            (new Attributes())
                ->setReadonly($this->setting->has('WEBSITE_CONTACT_EMAIL'))
                ->setRequired()
                ->setDefaultValue($this->setting->get('WEBSITE_CONTACT_EMAIL'))
        )->addSubmitButton('create', 'create');
    }
}
