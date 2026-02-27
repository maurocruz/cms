<?php
namespace Plinct\Cms\Http\View\Modules\Action;

use Plinct\Cms\Http\View\Abstracts\ModuleViewAbstract;
use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Cms\Http\View\Template\Template;

class ActionListView extends ModuleViewAbstract
{
	public function __construct(ComponentFactory $componentFactory, Template $template)
	{
		parent::__construct($componentFactory, $template);
		// NAVBAR
		$this->addNavbar(_('Action'),2,[
			'/admin/action' => $this->icon()->home(),
			'/admin/action/new' => $this->icon()->plus()
		]);
	}

	public function build(array $data = null): void
	{
		$querystrings = http_build_query($this->getQuerystrings());
		$apiHost = $this->getContext()->getApiHost();
		$params = $querystrings ? $querystrings."&properties=object" : "properties=object";
		$this->addMain(
			$this->reactShell('action')->setApiHost($apiHost)->setColumnsTable(['object'=>_('Object')])->setDataset('params',$params)->ready()
		);
	}
}
