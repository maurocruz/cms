<?php
namespace Plinct\Cms\Http\View\Modules\Action;

use Plinct\Cms\Http\View\Modules\Thing\ThingView;
use Plinct\Cms\Support\Support;

class ActionEditView extends ThingView
{

	public function build(array $data = null): void
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
		$this->addHeader(ActionComponentView::navbarItem($name, $idaction, $idobject));

		// CONTENT
		$form = $this->form('form-action', ['class'=>'form-basic form-action']);
		$this->addMain(
			$this->box()->expandingBox(_('Edit action'), ActionComponentView::formAction($form, 'edit', $data), true)
		);
	}
}
