<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Product;

use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Web\Element\Form;

abstract class ProductAbstract
{
    /**
     * @var string
     */
    protected string $id;
    /**
     * @var string
     */
    protected string $manufacturer;
    /**
     * @var string
     */
    protected string $manufacturerType;

    //use FormElementsTrait;

    protected function new(): array
    {
        return self::formProduct();
    }

    /**
     * @param string $case
     * @param null $value
     * @return array
     */
    protected function formProduct(string $case = "new", $value = null): array
    {
        $form = new Form(['class'=>'formPadrao form-product']);
        $form->action("/admin/product/$case")->method("post");

        $form->content("<h4>" . _(ucfirst($case)) . "</h4>");

        // HIDDENS
        $form->input('manufacturer',$this->manufacturer,'hidden');
        $form->input('manufacturerType',$this->manufacturerType,'hidden');
        if($case == 'edit') $form->input('id', $this->id,'hidden');
        // NAME
        $form->fieldsetWithInput('name', $value['name'] ?? null, _('name'));
        // CATEGORY
        $form->fieldset(Fragment::form()->selectCategory('Product', $value['category'] ?? null), _("Category"));
        // DESCRIPTION
        $form->fieldsetWithTextarea('description', $value['description'] ?? null, _("Description"));
        // DISAMBIGUATING DESCRIPTION
        $form->fieldsetWithTextarea('disambiguatingDescription', $value['disambiguatingDescription'] ?? null, _("Disambiguating description"));
        // SUBMIT BUTTONS
        $form->submitButtonSend();
        if ($case == 'edit') $form->submitButtonDelete("/admin/product/erase");
        // READY
        return $form->ready();
    }
}
