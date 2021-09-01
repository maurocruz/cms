<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Intangible\Service;

use Plinct\Cms\View\Fragment\FormFragment;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\Form;

abstract class ServiceAbstract
{
    use FormElementsTrait;

    /**
     * @var
     */
    protected $tableHasPart;
    /**
     * @var
     */
    protected $idHasPart;

    /**
     * @return array
     */
    protected function newWithPartOfForm(): array
    {
        return self::serviceForm();
    }

    /**
     * @param string $case
     * @param null $value
     * @return array
     */
    protected function serviceForm(string $case = "new", $value = null): array
    {
        $form = new Form(['class'=>'formPadrao form-service box']);
        $form->action("/admin/service/$case")->method("post");
        // title
        $title = $case == "edit" ? "Edit service" : "Add new service";
        $form->content("<h4>"._($title)."</h4>");
        // HIDDENS
        $form->input('provider',$this->idHasPart,'hidden')->input('providerType',$this->tableHasPart,'hidden');
        if ($case == 'edit') $form->input('id',ArrayTool::searchByValue($value['identifier'],'id','value'),'hidden');
        // NAME
        $form->fieldsetWithInput('name', $value['name'] ?? null, _('Name'));
        // ADDITIONAL TYPE
        $form->fieldset(FormFragment::selectAdditionalType('service', $value['additionalType'] ?? null), _("Additional type"));
        // CATEGORY
        $form->fieldset(FormFragment::selectCategory('service', $value['category'] ?? null), _("Category"));
        // DESCRIPTION
        $form->fieldsetWithTextarea('description', $value['description'] ?? null, _("Description"));
        // DISAMBIGUATING DESCRIPTION
        $form->fieldsetWithTextarea('disambiguatingDescription', $value['disambiguatingDescription'] ?? null, _("Disambiguating description"));
        // TERMS OF SERVICE
        $form->fieldsetWithTextarea('termsOfService', $value['termsOfService'] ?? null, _("Terms of service"));
        // SUBMIT BUTTONS
        $form->submitButtonSend();
        if ($case == "edit") $form->submitButtonDelete("/admin/service/erase");
        // RENDER
        return $form->ready();
    }
}