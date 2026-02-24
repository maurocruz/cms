<?php
namespace Plinct\Cms\Http\View\Abstracts;

use Plinct\Cms\Http\View\Contracts\ModulesViewInterface;

abstract class ModuleViewAbstract extends TemplateAbstract implements ModulesViewInterface
{
	/**
	 * @param string $title
	 * @param int $level
	 * @param array|null $tabs
	 * @param array|null $searchInput
	 * @return void
	 */
	protected function addNavbar(string $title, int $level = 2, array $tabs = null, array $searchInput = null): void
	{
		$this->addHeader(
			$this->navbar($title, $level, $tabs, $searchInput)->ready()
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
