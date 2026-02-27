<?php
namespace Plinct\Cms\Http\View\Component;

use Plinct\Cms\Http\View\Component\Box\Box;
use Plinct\Cms\Http\View\Component\Form\Form;
use Plinct\Cms\Http\View\Component\Navbar\Navbar;
use Plinct\Cms\Http\View\Component\ReactShell\ReactShell;
use Plinct\Cms\Http\View\Component\Table\Table;
use Plinct\Web\Fragment\Fragment;
use Plinct\Web\Fragment\Icons\IconsFragment;

class ComponentFactory
{
	/**
	 * @return Box
	 */
	public static function box(): Box
	{
		return new Box();
	}


	public static function icon(): IconsFragment {
		return Fragment::icons();
	}

	// FORM
	public function form(string $formName, array $attributes = null): Form
	{
		$form = new Form();
		$form->setComponentFactory($this);
		$form->setFormName($formName);
		$form->setAttributes($attributes);
		return $form;
	}

	// NAVBAR
	public static function navbar(string $title = null, array $tabs = null, int $level = 2, array $searchInput = null): Navbar
	{
		return new Navbar($title, $tabs, $level, $searchInput);
	}

	// REACT
	public function reactShell(string $type, array $attributes = []): ReactShell
	{
		return new ReactShell($type, $attributes);
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
