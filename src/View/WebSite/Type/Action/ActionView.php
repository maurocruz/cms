<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Action;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Product\ProductView;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class ActionView extends ThingView implements TypeViewInterface
{
	private ?string $querystring = null;

	/**
	 * @param string $type
	 */
	public function __construct(string $type = 'action')
	{
		parent::__construct($type);
	}

	/**
	 * @return void
	 */
	public function __destruct()
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('action')
				->title(_("Action"))
				->newTab('/admin/action?'.$this->querystring, CmsFactory::view()->fragment()->icon()->home(18,18))
				->newTab('/admin/action/new?'.$this->querystring, CmsFactory::view()->fragment()->icon()->plus(18,18))
				->search()
				->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		if($queryParams) {
			$this->querystring = http_build_query($queryParams);
		}
		if (isset($data['@type']) && $data['@type'] == "Product") {
			$tbProduct = CmsFactory::helpers()->typeBuilder($data);
			$name = $tbProduct->getValue('name');
			$idproduct = $tbProduct->getId();
			$idthing = $tbProduct->getPropertyValue('idthing');
			ProductView::navbarProduct($name, (string) $idproduct, $idthing);
		}
		$params = $queryParams ? http_build_query($queryParams)."&properties=object" : "properties=object";
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('action')->setColumnsTable(['object'=>_('Object')])->setDataset('params',$params)->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function new(?array $data, array $queryParams = null): void
	{
		self::hasPart($data, $queryParams);
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(self::formAction('new',$queryParams))
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
		if (!empty($data)) {
			$value = $data[0];
			$tbAction = CmsFactory::helpers()->typeBuilder($value);
			$idthing = $tbAction->getIdthing();
			if (is_array($value['object'])) {
				self::hasPart($value['object'], $queryParams);
			}
			// FORM
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox( _("Edit person"), self::formAction('edit', $value), true));
			// IMAGE OBJECT
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart((int)$idthing)->ready());
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("Action is not exists!")));
		}
	}

	private function hasPart(array $data, array $queryParams): void
	{
		if($queryParams) {
			$this->querystring = http_build_query($queryParams);
		}
		if (isset($data['@type']) && $data['@type'] == "Product") {
			$tbProduct = CmsFactory::helpers()->typeBuilder($data);
			$name = $tbProduct->getValue('name');
			$idproduct = $tbProduct->getId();
			$idthing = $tbProduct->getPropertyValue('idthing');
			ProductView::navbarProduct($name, (string) $idproduct, $idthing);
		}
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
			$tbobject = CmsFactory::helpers()->typeBuilder($object);
			$object = $tbobject->getPropertyValue('idthing');
		}
		$provider = $value['provider'] ?? null;
		$result = $value['result'] ?? null;
		$startTime = $value['startTime'] ?? null;
		$targetCollection = $value['targetCollection'] ?? null;
		$form = CmsFactory::view()->fragment()->form("form-action",['class'=>'form-basic form-action']);
		$form->action("/admin/action/$case")->method('post');
		$form->addMandatories('agent','object');
		if ($case == 'edit') {
			$typeBuilder = new TypeBuilder('action', $value);
			$idaction = $typeBuilder->getId();
			$form->input('idaction', (string) $idaction, 'hidden');
		} else {
			$form->content("<h3>".sprintf(_('Add new %s'),_('Action'))."</h3>");
		}
		// THING
		$form = parent::formThingContent($form, $value);
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