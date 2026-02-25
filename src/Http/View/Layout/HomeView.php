<?php
namespace Plinct\Cms\Http\View\Layout;

use Plinct\Cms\Http\View\Abstracts\TemplateViewAbstract;

class HomeView extends TemplateViewAbstract
{

	public function build(array $data = null): void
	{
		$this->addMain("<h1>Home</h1>");
	}
}
