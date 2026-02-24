<?php
namespace Plinct\Cms\Http\View\Abstracts;

use Plinct\Cms\Http\View\Component\Box\Box;
use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Cms\Http\View\Component\Form\Form;
use Plinct\Cms\Http\View\Component\Navbar\Navbar;
use Plinct\Cms\Http\View\Component\Table\Table;
use Plinct\Cms\Http\View\Contracts\ComponentInterface;
use Plinct\Web\Fragment\Icons\IconsFragment;

abstract class ComponentAbstract implements ComponentInterface
{
	public function __construct(private readonly ComponentFactory $componentFactory)
	{
	}

	public function box(): Box
	{
		return $this->componentFactory->box();
	}

	public function icon(): IconsFragment {
		return $this->componentFactory->icon();
	}

	public function form(string $formName, array $attributes = null): Form
	{
		return $this->componentFactory->form($formName, $attributes);
	}

	public function navbar(string $title = null, int $level = 2, array $tabs = null, array $searchInput = null): Navbar
	{
		return $this->componentFactory->navbar($title, $tabs, $level, $searchInput);
	}

	public function table(array $attributes = null): Table
	{
		return $this->componentFactory->table($attributes);
	}
}
