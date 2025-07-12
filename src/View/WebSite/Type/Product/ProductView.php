<?php
namespace Plinct\Cms\View\WebSite\Type\Product;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class ProductView extends ThingView implements TypeViewInterface
{
	/**
	 * @var string|null
	 */
	private ?string $name = null;
	/**
	 * @var string|null
	 */
	private ?string $idproduct = null;

	private ?string $thing = null;

	/**
	 *
	 */
	public function __destruct()
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_('Product'))
				->type('product')
				->level(2)
				->newTab("/admin/product", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/product/new", CmsFactory::view()->fragment()->icon()->plus())
				->ready()
		);
		if ($this->name && $this->idproduct && $this->thing) {
			CmsFactory::view()->addHeader(
				CmsFactory::view()->fragment()->navbar()
					->title($this->name)
					->type('product')
					->level(3)
					->newTab("/admin/product/edit/$this->idproduct", CmsFactory::view()->fragment()->icon()->home())
					->newTab("/admin/action?object=$this->thing", CmsFactory::view()->fragment()->icon()->action())
					->ready()
			);
		}
	}

	/**
	 * @param string $name
	 * @param string $idproduct
	 * @param string $thing
	 * @return void
	 */
	public static function navbarProduct(string $name, string $idproduct, string $thing): void
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_('Product'))
				->type('product')
				->level(2)
				->newTab("/admin/product", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/product/new", CmsFactory::view()->fragment()->icon()->plus())
				->ready()
		);
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title($name)
				->type('product')
				->level(3)
				->newTab("/admin/product/edit/$idproduct", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/action?object=$thing", CmsFactory::view()->fragment()->icon()->action())
				->ready()
		);

	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('product')->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function new(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox($this->formProduct())
		);
	}

  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @throws Exception
   */
  public function edit(?array $data, array $queryParams = null): void
  {
		if (isset($data[0])) {
			$value = $data[0];
			$typeBuilder = ToolBox::typeBuilder($value);
			$this->idproduct = $typeBuilder->getId();
			$this->thing = $typeBuilder->getPropertyValue('idthing');
			$this->name = $value['name'];

			CmsFactory::view()->addMain([
					CmsFactory::view()->fragment()->box()->simpleBox(self::formProduct($value)),
					CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart($this->thing)->ready()
				]
			);
		} else {
			CmsFactory::view()->addMain("<p class='warning'>"._('Product Not Found')."</p>");
		}
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
		$form = CmsFactory::view()->fragment()->form("form-product");
		$form->attributes(['class'=>'form-basic form-product']);
		$form->method('post');
		if ($value) {
			$typeBuilder = ToolBox::typeBuilder($value);
			$idproduct = $typeBuilder->getId();
			$form->action('/admin/product/edit');
			$form->input('idproduct', (string) $idproduct, 'hidden');
		} else {
			$form->action('/admin/product/new');
		}
		$form = parent::formThing($form, $value);
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
