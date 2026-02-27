<?php
namespace Plinct\Cms\Http\View\Modules\Product;

use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Cms\Http\View\Component\Form\Form;
use Plinct\Cms\Http\View\Contracts\ModuleComponentViewInterface;
use Plinct\Cms\Http\View\Modules\Thing\ThingComponentView;
use Plinct\Cms\Support\Support;

class ProductComponentView implements ModuleComponentViewInterface
{

	public static function navbar(array $querystrings = null): array
	{
		$navbar = ComponentFactory::navbar();
		$navbar->title(_('Products'));
		$navbar->newTab("/admin/product", ComponentFactory::icon()->home());
		$navbar->newTab("/admin/product/new", ComponentFactory::icon()->plus());
		return $navbar->ready();
	}

	public static function navbarItem(string $name, string $id, array $querystrings = null): array
	{
		$string = http_build_query($querystrings);
		$navbar = ComponentFactory::navbar();
		$navbar->title(_($name));
		$navbar->level(3);
		$navbar->newTab("/admin/product/edit/$id", ComponentFactory::icon()->home());
		$navbar->newTab("/admin/action". ($string ? "?".$string : ''), ComponentFactory::icon()->action());
		return [
			self::navbar(),
			$navbar->ready()
		];
	}

	public static function navbarParent(string $name, string $id, string $nameParent, string $idparent, array $queryStrings = null): array
	{
		return [];
	}

	public static function form(Form $form, string $case = 'new', array $value = null): array
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
		$form = ThingComponentView::formFragment($form, $value);
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

	public static function formFragment(Form $form, array $value = null): Form
	{
		return $form;
	}
}
