<?php
namespace Plinct\Cms\Http\View\Modules\Action;

use Plinct\Cms\Http\Support\SupportHttp;
use Plinct\Cms\Http\View\Abstracts\ModuleViewAbstract;
use Plinct\Cms\Http\View\Modules\Product\ProductComponentView;

class ActionIndexView extends ModuleViewAbstract
{

	public function build(array $data = null): void
	{
		// OBJECT
		$objectData = $data['object'] ?? null;
		$idthingObject = null;
		if ($objectData) {
			$object = SupportHttp::typeBuilder($objectData);
			$type = $object->getType();
			$name = $object->getValue('name');
			$idobject = $object->getId();
			$idthingObject = $object->getIdthing();
			if ($type == 'Product') {
				$this->addNavbar(ProductComponentView::navbarItem($name, $idobject, $idthingObject));
			}
		}
		// NAVBAR ACTION
		$this->addNavbar(_('Action'),4,[
			('/admin/action'.($idthingObject ? "?object=$idthingObject" : "")) => $this->icon()->home(),
			('/admin/action/new'.($idthingObject ? "?object=$idthingObject" : "")) => $this->icon()->plus()
		]);

		// CONTENT
		$params = http_build_query($this->getQuerystrings());
		$apiHost = $this->getContext()->getApiHost();
		$this->addMain(
			$this->reactShell('action')->setApiHost($apiHost)->setColumnsTable(['object'=>_('Object')])->setDataset('params',$params)->ready()
		);
	}
}
