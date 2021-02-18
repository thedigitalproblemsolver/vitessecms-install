<?php declare(strict_types=1);

namespace VitesseCms\Install\Forms;

use VitesseCms\Core\Models\Datagroup;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\AbstractFormWithRepository;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Interfaces\FormWithRepositoryInterface;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Install\Repositories\AdminRepositoryCollectionInterface;

class AffiliateForm extends AbstractFormWithRepository
{
    /**
     * @var AdminRepositoryCollectionInterface
     */
    protected $repositories;

    public function buildForm(): FormWithRepositoryInterface
    {
        $this->addDropdown(
            'Affiliate property datagroup',
            'SHOP_DATAGROUP_AFFILIATE',
            (new Attributes())->setRequired()
                ->setOptions(ElementHelper::modelIteratorToOptions($this->repositories->datagroup->findAll()))
                ->setDefaultValue($this->setting->get('SHOP_DATAGROUP_AFFILIATE')
                ))
            ->addSubmitButton('create', 'create');

        return $this;
    }
}
