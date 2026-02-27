<?php
namespace Plinct\Cms\Http\View\Modules\Product;

use Plinct\Cms\Http\View\Modules\Thing\ThingView;
use Plinct\Cms\Support\Support;

class ProductShowView extends ThingView
{

	public function build(array $data = null): void
	{
		$typeValue = Support::typeBuilder($data);
		$this->idthing = $typeValue->getIdthing();
		$name = $typeValue->getValue('name');
		$idproduct = $typeValue->getId();

		$this->addNavbar(_($name),3,[
			('/admin/product/edit/'.$idproduct) => $this->icon()->home(),
			('/admin/action?object='.$this->idthing) => $this->icon()->action()
		]);
		$this->addMain([
			$this->box()->simpleBox($this->formProduct($data),_('Product')),
			$this->reactShell('imageObject')->setIdHasPart($this->idthing)->setProperty('hasPart')->ready()
		]);
	}

	public function new(): void
	{
		$this->type = 'product';
		$this->addMain([
			$this->box()->simpleBox($this->formProduct(),_('New product')),
		]);
	}

	/**
	 * @param array|null $value
	 * @return array
	 */
	private function formProduct(?array $value = null): array
	{
		$brand = $value['brand'] ?? null;
		$category = $value['category'] ?? null;
		$keywords = $value['keywords'] ?? null;
		$manufacturer = $value['manufacturer'] ?? null;
		$material = $value['material'] ?? null;
		$color = $value['color'] ?? null;
		$mpn = $value['mpn'] ?? null;
		$model = $value['model'] ?? null;
		$productionDate = $value['productionDate'] ?? null;
		$purchaseDate = $value['purchaseDate'] ?? null;
		$form = $this->form("form-product");
		$form->attributes(['class'=>'form-basic form-product']);
		$form->method('post');
		if ($value) {
			$typeBuilder = Support::typeBuilder($value);
			$idproduct = $typeBuilder->getId();
			$form->action('/admin/product/update');
			$form->input('idproduct', (string) $idproduct, 'hidden');
		} else {
			$form->action('/admin/product/create');
		}
		// THING FORM
		$form = $this->formThing($form, $value);
		// manufacturer
		$form->relationshipOneToOne('Organization',_('Manufacturer'),'manufacturer', $manufacturer);
		// brand
		$form->fieldsetWithInput('brand', $brand, _('Brand'));
		// MODEL
		$form->fieldsetWithInput('model', $model, _('Model'));
		// MATERIAL
		$form->fieldsetWithInput('material', $material, _('Material'));
		// COLOR
		$form->fieldsetWithInput('color', $color, _('Color'));
		// MPN
		$form->fieldsetWithInput('mpn', $mpn, _('MPN'));
		// category
		$form->fieldsetWithInput('category', $category, _('Category'));
		// KEYWORDS
		$form->fieldsetWithInput('keywords', $keywords, _('Keywords'));
		// PRODUCTION DATE
		$form->fieldsetWithInput('productionDate', $productionDate, _('Production date'), 'date');
		// PURCHASE DATE
		$form->fieldsetWithInput('purchaseDate', $purchaseDate, _('Purchase date'), 'date');
		// SUBMIT
		$form->submitButtonSend();
		if ($value) {
			$form->submitButtonDelete('/admin/product/delete');
		}
		return $form->ready();
	}
}
