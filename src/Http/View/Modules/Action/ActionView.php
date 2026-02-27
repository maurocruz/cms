<?php
namespace Plinct\Cms\Http\View\Modules\Action;

use Plinct\Cms\Http\Support\SupportHttp;
use Plinct\Cms\Http\View\Modules\Product\ProductComponentView;
use Plinct\Cms\Http\View\Modules\Thing\ThingView;
use Plinct\Cms\Support\Support;

class ActionView extends ThingView
{
	public function index(array $data = null): void
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
				$this->addHeader(
					ProductComponentView::navbarItem($name, $idobject, ['object'=>$idthingObject])
				);
			}
		}
		// NAVBAR ACTION
		$this->addHeader(ActionComponentView::navbar(['object'=>$idthingObject]));
		// CONTENT
		$params = http_build_query($this->getQuerystrings());
		$apiHost = $this->getContext()->getApiHost();
		$this->addMain(
			$this->reactShell('action')->setApiHost($apiHost)->setColumnsTable(['object'=>_('Object')])->setDataset('params',$params)->ready()
		);
	}

	public function new(array $data = null): void
	{
		// NAVBAR
		$object = $data['object'] ?? null;
		$idthingObject = null;
		if ($object) {
			$ObjectBilder = Support::typeBuilder($object);
			$idthingObject = $ObjectBilder->getIdthing();
			$this->addHeader(ActionComponentView::navbarObject($object));
		}

		$this->addHeader(
			ActionComponentView::navbar($idthingObject)
		);

		// FORM
		$form = $this->form('form-action', ['class'=>'form-basic form-action']);
		$this->addMain(
			ActionComponentView::form($form, 'new', $data)
		);
	}

	public function edit(array $data = null): void
	{
		$typeValue = Support::typeBuilder($data);
		$idthing = $typeValue->getIdthing();
		$this->setIdthing($idthing);
		$this->setType($typeValue->getType());
		$name = $typeValue->getValue('name');
		$idaction = $typeValue->getId();
		$object = $data['object'] ?? null;
		$idobject = null;
		if ($object) {
			$ObjectBilder = Support::typeBuilder($object);
			$idobject = $ObjectBilder->getIdthing();
			$this->addHeader(ActionComponentView::navbarObject($object));
		}
		// NAVBAR
		$this->addHeader(ActionComponentView::navbarItem($name, $idaction, ['object' => $idobject]));

		// CONTENT
		$form = $this->form('form-action', ['class'=>'form-basic form-action']);
		$this->addMain(
			$this->box()->expandingBox(_('Edit action'), ActionComponentView::form($form, 'edit', $data), true)
		);
	}
}
