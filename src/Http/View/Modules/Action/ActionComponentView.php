<?php

namespace Plinct\Cms\Http\View\Modules\Action;

use Plinct\Cms\Http\Support\SupportHttp;
use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Cms\Http\View\Component\Form\Form;
use Plinct\Cms\Http\View\Component\Navbar\Navbar;
use Plinct\Cms\Http\View\Modules\Product\ProductComponentView;
use Plinct\Cms\Http\View\Modules\Thing\ThingComponentView;
use Plinct\Cms\Support\Support;

class ActionComponentView
{
	public static function navbarObject(array $objectData): array
	{
		$object = SupportHttp::typeBuilder($objectData);
		$type = $object->getType();
		$name = $object->getValue('name');
		$idobject = $object->getId();
		$idthingObject = $object->getIdthing();
		if ($type == 'Product') {
			return ProductComponentView::navbarItem($name, $idobject, $idthingObject);
		}
		return [];
	}

	public static function navbar(string $idobject = null):array
	{
		$queryObject = $idobject ? "?object=$idobject" : "";
		$navbar = new Navbar();
		$navbar->title(_('Action'));
		$navbar->newTab("/admin/action".$queryObject, ComponentFactory::icon()->home());
		$navbar->newTab("/admin/action/new".$queryObject, ComponentFactory::icon()->plus());
		return $navbar->ready();
	}

	public static function navbarItem($name, $idaction, $idobject): array
	{
		$navbar = new Navbar();
		$navbar->title(_($name));
		$navbar->level(3);
		$navbar->newTab("/admin/action/edit/$idaction", ComponentFactory::icon()->home());
		return [
			self::navbar($idobject),
			$navbar->ready()
		];
	}


	/**
	 * @param Form $form
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	public static function formAction(Form $form, string $case = 'new', array $value = null): array
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

		$form->action("/admin/action/$case")->method('post');
		$form->addMandatories(['agent','object']);
		if ($case == 'edit') {
			$typeBuilder = Support::typeBuilder($value);
			$idaction = $typeBuilder->getId();
			$form->input('idaction', (string) $idaction, 'hidden');
		}
		// THING
		$form = ThingComponentView::formThing($form, $value);
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
		return ComponentFactory::box()->expandingBox(_('Edit action'), $form->ready(), true);
	}
}
