<?php
namespace Plinct\Cms\Http\View\Component;

use Plinct\Cms\Http\View\Component\Form\Form;
use Plinct\Cms\Http\View\Component\Navbar\Navbar;
use Plinct\Web\Fragment\Fragment;
use Plinct\Web\Fragment\Icons\IconsFragment;

class ComponentFactory
{
	/**
	 * @param string|null $title
	 * @param array|null $tabs
	 * @param int $level
	 * @param array|null $searchInput
	 * @return Navbar
	 */
	public function navbar(string $title = null, array $tabs = null, int $level = 2, array $searchInput = null): Navbar
	{
		return new Navbar($title, $tabs, $level, $searchInput);
	}

	/**
	 * @return IconsFragment
	 */
	public function icon(): IconsFragment {
		return Fragment::icons();
	}

	public function form(string $formName, array $attributes = null): Form
	{
		return new Form($formName, $attributes);
	}

}
