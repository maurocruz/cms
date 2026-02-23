<?php
namespace Plinct\Cms\Http\View\Layout;

use Plinct\Cms\Http\View\Abstracts\ViewAbstract;

class HomeView extends ViewAbstract
{

	public function build(array $params = null): void
	{
		$this->addMain("<h1>Home</h1>");
	}
}
