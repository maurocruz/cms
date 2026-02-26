<?php
namespace Plinct\Cms\Http\View\Modules\Action;

use Plinct\Cms\Http\View\Abstracts\ModuleViewAbstract;

class ActionListView extends ModuleViewAbstract
{
	public function build(array $data = null): void
	{
		$querystrings = http_build_query($this->getQuerystrings());
		$apiHost = $this->getContext()->getApiHost();
		$this->addNavbar(_('Action'),2,[
			'/admin/action?'.$querystrings => $this->icon()->home(),
			'/admin/action/new?'.$querystrings => $this->icon()->plus(18,18)
		]);

		$params = $querystrings ? $querystrings."&properties=object" : "properties=object";
		$this->addMain(
			$this->reactShell('action')->setApiHost($apiHost)->setColumnsTable(['object'=>_('Object')])->setDataset('params',$params)->ready()
		);
	}
}
