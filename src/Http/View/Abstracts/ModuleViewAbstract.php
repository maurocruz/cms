<?php
namespace Plinct\Cms\Http\View\Abstracts;

use Plinct\Cms\Http\View\Component\Navbar\Navbar;
use Plinct\Cms\Http\View\Contracts\ModulesViewInterface;

abstract class ModuleViewAbstract extends ComponentAbstract implements ModulesViewInterface
{
	/**
	 * @param string|Navbar|array $title
	 * @param int $level
	 * @param array|null $tabs
	 * @param array|null $searchInput
	 * @return void
	 */
	protected function addNavbar(string|Navbar|array $title, int $level = 2, array $tabs = null, array $searchInput = null): void
	{
		$this->addHeader(
			is_string($title)
				? $this->navbar($title, $level, $tabs, $searchInput)->ready()
				: (is_array($title) ? $title : $title->ready())
		);
	}

	/**
	 * @param $content
	 * @return void
	 */
	public function addHeader($content): void {
		$this->template->addHeader($content);
	}
}
