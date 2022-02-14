<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\Service;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

abstract class ServiceAbstract
{
    use FormElementsTrait;

    /**
     * @var string
     */
    protected string $tableHasPart;
    /**
     * @var string
     */
    protected string $idHasPart;

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
        $form = Fragment::form(['class'=>'formPadrao form-service box']);
        $form->action("/admin/service/$case")->method("post");
        // title
        $title = $case == "edit" ? "Edit service" : "Add new service";
        $form->content("<h4>"._($title)."</h4>");
        // HIDDENS
        $form->input('provider',$this->idHasPart,'hidden')->input('providerType',$this->tableHasPart,'hidden');
        if ($case == 'edit') $form->input('id', ArrayTool::searchByValue($value['identifier'],'id','value'),'hidden');
        // NAME
        $form->fieldsetWithInput('name', $value['name'] ?? null, _('Name'));
        // ADDITIONAL TYPE
        $form->selectAdditionalType('service', $value['additionalType'] ?? null);
        // CATEGORY
        $form->selectCategory('service', $value['category'] ?? null);
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