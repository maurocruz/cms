<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\WebSite\Type\Product;

use Plinct\Cms\Controller\CmsFactory;

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

	/**
	 * @return array
	 */
  protected function new(): array {
      return self::formProduct();
  }

  /**
   * @param string $case
   * @param null $value
   * @return array
   */
  protected function formProduct(string $case = "new", $value = null): array
  {
    $form = CmsFactory::response()->fragment()->form(['class'=>'formPadrao form-product']);
    $form->action("/admin/product/$case")->method("post");

    $form->content("<h4>" . _(ucfirst($case)) . "</h4>");
    // HIDDEN
    $form->input('manufacturer',$this->manufacturer,'hidden');
    $form->input('manufacturerType',$this->manufacturerType,'hidden');
    if($case == 'edit') $form->input('idproduct', $this->id,'hidden');
    // NAME
    $form->fieldsetWithInput('name', $value['name'] ?? null, _('name'));
    // ADDITIONAL TYPE
    $form->fieldset(CmsFactory::response()->fragment()->form()->selectAdditionalType('Product', $value['additionalType'] ?? null), _('Additional Type'));
    // CATEGORY
    $form->fieldset(CmsFactory::response()->fragment()->form()->selectCategory('Product', $value['category'] ?? null), _("Category"));
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
