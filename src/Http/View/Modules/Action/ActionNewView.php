<?php
namespace Plinct\Cms\Http\View\Modules\Action;

use Plinct\Cms\Http\View\Abstracts\ModuleViewAbstract;
use Plinct\Cms\Support\Support;

class ActionNewView extends ModuleViewAbstract
{

	public function build(array $data = null): void
	{
		// NAVBAR
		$object = $data['object'] ?? null;
		$idthingObject = null;
		if ($object) {
			$ObjectBilder = Support::typeBuilder($object);
			$idthingObject = $ObjectBilder->getIdthing();
			$this->addHeader(ActionComponentView::navbarObject($object));
		}
		$this->addNavbar(ActionComponentView::navbar($idthingObject));

		// FORM
		$form = $this->form('form-action', ['class'=>'form-basic form-action']);
		$this->addMain(
			ActionComponentView::formAction($form, 'new', $data)
		);
	}
}
