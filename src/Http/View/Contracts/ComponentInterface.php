<?php
namespace Plinct\Cms\Http\View\Contracts;

use Plinct\Cms\Http\View\Component\Box\Box;
use Plinct\Cms\Http\View\Component\Form\Form;
use Plinct\Cms\Http\View\Component\Navbar\Navbar;
use Plinct\Cms\Http\View\Component\ReactShell\ReactShell;
use Plinct\Cms\Http\View\Component\Table\Table;
use Plinct\Web\Fragment\Icons\IconsFragment;

interface ComponentInterface
{
	public function box(): Box;
	public function icon(): IconsFragment;
	public function form(string $formName, array $attributes = null): Form;
	public function navBar(string $title = null, int $level = 2, array $tabs = null, array $searchInput = null): Navbar;
	public function reactShell(string $type, array $attributes = []): ReactShell;
	public function table(array $attributes = null): Table;
}
