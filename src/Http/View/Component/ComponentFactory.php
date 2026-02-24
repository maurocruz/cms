<?php
namespace Plinct\Cms\Http\View\Component;

use Plinct\Cms\Http\View\Component\Box\Box;
use Plinct\Cms\Http\View\Component\Form\Form;
use Plinct\Cms\Http\View\Component\Navbar\Navbar;
use Plinct\Cms\Http\View\Component\Table\Table;
use Plinct\Web\Fragment\Fragment;
use Plinct\Web\Fragment\Icons\IconsFragment;

class ComponentFactory
{
	/**
	 * @return Box
	 */
	public function box(): Box
	{
		return new Box();
	}


	public function icon(): IconsFragment {
		return Fragment::icons();
	}

	// FORM
	public function form(string $formName, array $attributes = null): Form
	{
		return new Form($formName, $attributes);
	}

	// NAVBAR
	public function navbar(string $title = null, array $tabs = null, int $level = 2, array $searchInput = null): Navbar
	{
		return new Navbar($title, $tabs, $level, $searchInput);
	}

	/**
	 * @param array|null $attributes
	 * @return Table
	 */
	public function table(array $attributes = null): Table
	{
		return new Table($attributes);
	}
}
