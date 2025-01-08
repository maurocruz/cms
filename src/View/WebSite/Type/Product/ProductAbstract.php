<?php
namespace Plinct\Cms\View\WebSite\Type\Product;

use Plinct\Cms\CmsFactory;

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

	protected function navbarIndex(?string $title = null): void
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_('Product'))
				->type('product')
				->level(4)
				->newTab("/admin/product?manufacturer=$this->manufacturer", CmsFactory::view()->fragment()->icon()->home(16,16))
				->newTab("/admin/product/new?manufacturer=$this->manufacturer", CmsFactory::view()->fragment()->icon()->plus(16,16))
				->ready()
		);
		if ($title) {
			CmsFactory::view()->addHeader(
				CmsFactory::view()->fragment()->navbar()->title($title)->level(3)->ready()
			);
		}
	}

	/**
	 * @param string|null $title
	 */
	protected function navbarProduct(string $title = null)
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar(_("Product"), [
				"/admin/product" => CmsFactory::view()->fragment()->icon()->home(16,16),
				"/admin/product/new" => CmsFactory::view()->fragment()->icon()->plus(16,16)
			], 4, ['table'=>'product'] )
				->level(2)
				->ready()
		);

		if ($title) {
			CmsFactory::view()->fragment()->navbar($title, [], 5);
		}
	}

  /**
   * @param string $case
   * @param null $value
   * @return array
   */
  protected function formProduct(string $case = "new", $value = null): array
  {
    $form = CmsFactory::view()->fragment()->form(['class'=>'formPadrao form-product']);
    $form->action("/admin/product/$case")->method("post");

    $form->content("<h4>" . _(ucfirst($case)) . "</h4>");
    // HIDDEN
    $form->input('manufacturer',$this->manufacturer,'hidden');
    $form->input('manufacturerType',$this->manufacturerType,'hidden');
    if($case == 'edit') $form->input('idproduct', $this->id,'hidden');
    // NAME
    $form->fieldsetWithInput('name', $value['name'] ?? null, _('name'));
    // ADDITIONAL TYPE
    $form->fieldset(CmsFactory::view()->fragment()->form()->selectAdditionalType('Product', $value['additionalType'] ?? null), _('Additional Type'));
    // CATEGORY
    $form->fieldset(CmsFactory::view()->fragment()->form()->selectCategory('Product', $value['category'] ?? null), _("Category"));
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
