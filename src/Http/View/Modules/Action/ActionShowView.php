<?php
namespace Plinct\Cms\Http\View\Modules\Action;

use Plinct\Cms\Http\View\Modules\ThingView;
use Plinct\Cms\Http\View\Template\Template;
use Plinct\Cms\Support\Support;

class ActionShowView extends ThingView
{
	public function __construct(Template $template)
	{
		parent::__construct($template);

		$this->addNavbar(_('Action'),2,[
			'/admin/action' => $this->icon()->home(),
			'/admin/action/new' => $this->icon()->plus()
		]);
	}

	public function build(array $data = null): void
	{
		$typeValue = Support::typeBuilder($data);
		$this->setIdthing($typeValue->getIdthing());
		$this->setType($typeValue->getType());
		$name = $typeValue->getValue('name');
		$idaction = $typeValue->getId();
		// NAVBAR
		$this->addNavbar(_($name),3,[
			'/admin/action/edit/'.$idaction => $this->icon()->home(),
		]);
		$this->addMain(
			$this->box()->expandingBox(_('Edit action'),$this->formAction('edit', $data), true)
		);
	}

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	private function formAction(string $case = 'new', array $value = null): array
	{
		$actionProcess = $value['actionProcess'] ?? null;
		$actionStatus = $value['actionStatus'] ?? null;
		$agent = $value['agent'] ?? null;
		$endTime = $value['endTime'] ?? null;
		$object = $value['object'] ?? null;
		if (is_array($object)) {
			$tbobject = Support::typeBuilder($object);
			$object = $tbobject->getPropertyValue('idthing');
		}
		$provider = $value['provider'] ?? null;
		$result = $value['result'] ?? null;
		$startTime = $value['startTime'] ?? null;
		$targetCollection = $value['targetCollection'] ?? null;
		$form = $this->form("form-action",['class'=>'form-basic form-action']);
		$form->action("/admin/action/$case")->method('post');
		$form->addMandatories(['agent','object']);
		if ($case == 'edit') {
			$typeBuilder = Support::typeBuilder($value);
			$idaction = $typeBuilder->getId();
			$form->input('idaction', (string) $idaction, 'hidden');
		} else {
			$form->content("<h3>".sprintf(_('Add new %s'),_('Action'))."</h3>");
		}
		// THING
		$form = $this->formThing($form, $value);
		// OBJECT
		$form->relationshipOneToOne('thing', _('Object'), 'object', (int) $object);
		// ACTION PROCESS
		$form->fieldsetWithInput('actionProcess', $actionProcess, _('Process'));
		// ACTION STATUS
		$form->fieldsetWithSelect('actionStatus', $actionStatus, [
			'ActiveActionStatus' => _('Active'),
			'CompletedActionStatus' => _('Completed'),
			'FailedActionStatus' => _('Failed'),
			'PotentialActionStatus' => _('Potential'),
			'SuspendedActionStatus' => _('Suspended'),
			'CancelActionStatus' => _('Canceled'),
			'DeleteActionStatus' => _('Deleted'),
		], _('Status'));
		// AGENT
		$form->relationshipOneToOne('person,organization',_('Agent'),'agent', $agent);
		// PROVIDER
		$form->relationshipOneToOne('organization,person',_('Provider'),'provider', $provider);
		// RESULT
		$form->fieldsetWithTextarea('result', $result, _('Result'));
		// TARGET COLLECTION
		$form->fieldsetWithInput('targetCollection', $targetCollection, _('Collection'));
		// START TIME
		$form->fieldsetWithInput('startTime', $startTime, _("Start time"), 'datetime-local');
		// END TIME
		$form->fieldsetWithInput('endTime', $endTime, _("End time"), 'datetime-local');
		// SUBMIT
		if ($case == 'edit') {
			$form->submitButtonDelete('/admin/action/delete');
		}
		$form->submitButtonSend();
		// READY
		return $form->ready();
	}
}
