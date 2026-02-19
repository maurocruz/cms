<?php
namespace Plinct\Cms\Http\View\Fragment;

use Plinct\Cms\Http\View\Fragment\Navbar\Navbar;
use Plinct\Web\Fragment\Fragment;
use Plinct\Web\Fragment\Icons\IconsFragment;

class FragmentFactory
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

}
