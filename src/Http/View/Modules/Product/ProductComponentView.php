<?php
namespace Plinct\Cms\Http\View\Modules\Product;

use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Cms\Http\View\Component\Navbar\Navbar;

class ProductComponentView
{

	public static function navbar() : Navbar
	{
		$navbar = ComponentFactory::navbar();
		$navbar->title(_('Products'));
		$navbar->newTab("/admin/product", ComponentFactory::icon()->home());
		$navbar->newTab("/admin/product/new", ComponentFactory::icon()->plus());
		return $navbar;
	}

	public static function navbarItem($name, $idobject, $idthing): array
	{
		$navbar = ComponentFactory::navbar();
		$navbar->title(_($name));
		$navbar->level(3);
		$navbar->newTab("/admin/product/edit/$idobject", ComponentFactory::icon()->home());
		$navbar->newTab("/admin/action?object=$idthing", ComponentFactory::icon()->action());
		return [
			self::navbar()->ready(),
			$navbar->ready()
		];
	}
}
