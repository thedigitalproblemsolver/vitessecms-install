<?php declare(strict_types=1);

namespace VitesseCms\Install\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;

class newPropertyForm extends AbstractForm
{

    public function initialize()
    {
        $this->addEmail('%CORE_EMAIL%', 'email', (new Attributes())->setRequired())
            ->addPassword('%USER_PASSWORD%', 'password', (new Attributes())->setRequired())
            ->addSubmitButton('create new property', 'create');
    }
}
