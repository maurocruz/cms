<?php
namespace Plinct\Cms\Http\View\Modules\Product;

use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Cms\Http\View\Modules\Thing\ThingView;
use Plinct\Cms\Http\View\Template\Template;

class ProductListView extends ThingView
{
	public function __construct(ComponentFactory $componentFactory, Template $template)
	{
		parent::__construct($componentFactory, $template);

		// NAVBAR
		$this->addNavbar(_('Product'),2,[
			('/admin/product') => $this->icon()->home(),
			('/admin/product/new') => $this->icon()->plus()
		]);
	}

	public function build(array $data = null): void
	{
		$this->addMain(
			$this->reactShell('product')->ready()
		);
	}

}
