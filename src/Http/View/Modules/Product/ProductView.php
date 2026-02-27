<?php
namespace Plinct\Cms\Http\View\Modules\Product;

use Plinct\Cms\Http\View\Modules\Thing\ThingView;
use Plinct\Cms\Support\Support;

class ProductView extends ThingView
{

	public function index(array $data = null): void
	{
		// NAVBAR
		$this->addHeader(ProductComponentView::navbar());

		// CONTENT MAIN
		$this->addMain(
			$this->reactShell('product')->ready()
		);
	}

	public function new(array $data = null): void
	{
		$this->type = 'product';
		$form = $this->form("form-product",['class'=>'form-basic form-product']);
		$this->addMain([
			$this->box()->simpleBox(ProductComponentView::form($form),_('New product')),
		]);
	}

	public function edit(array $data = null): void
	{
		$typeValue = Support::typeBuilder($data);
		$this->idthing = $typeValue->getIdthing();
		$name = $typeValue->getValue('name');
		$idproduct = $typeValue->getId();

		// NAVBAR
		$this->addHeader(
			ProductComponentView::navbarItem($name, $idproduct, ["object"=>$this->idthing])
		);

		// CONTENT MAIN
		$form = $this->form("form-product",['class'=>'form-basic form-product']);
		$this->addMain([
			$this->box()->simpleBox(ProductComponentView::form($form,'edit',$data),_('Product')),
			$this->reactShell('imageObject')->setIdHasPart($this->idthing)->setProperty('hasPart')->ready()
		]);
	}
}
