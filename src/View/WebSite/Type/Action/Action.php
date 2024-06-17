<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Action;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class Action implements TypeInterface
{
	/**
	 * @param string|null $title
	 * @return void
	 */
	private function navbar(string $title = null)
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('action')
				->title(_("Action"))
				->newTab('/admin/action', CmsFactory::view()->fragment()->icon()->home(18,18))
				->newTab('/admin/action/new', CmsFactory::view()->fragment()->icon()->plus(18,18))
				->search()
				->ready()
		);
		if ($title !== null) {
			CmsFactory::view()->addHeader(
				CmsFactory::view()->fragment()->navbar()
					->title($title)
					->level(3)
					->ready()
			);
		}
	}

	/**
	 * @param array|null $value
	 * @return void
	 */
	public function index(?array $value)
	{
		$this->navbar();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('Action')->setColumnsTable(['@type'=>_('Types')])->ready()
		);
	}

	/**
	 * @param array|null $value
	 * @return void
	 */
	public function new(?array $value)
	{
		$this->navbar(_('Add new'));

		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox($this->form())
		);
	}

	/**
	 * @param array|null $data
	 * @return void
	 */
	public function edit(?array $data)
	{
		$this->navbar();
		if (!empty($data)) {
			$value = $data[0];
			// FORM
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox( _("Edit person"), self::form('edit', $value), true));
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("Action is not exists!")));
		}
	}

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	private function form(string $case = 'new', array $value = null): array
	{
		$agent = $value['agent'] ?? null;
		$object = $value['object'] ?? null;
		$participant = $value['participant'] ?? null;
		$startTime = $value['startTime'] ?? null;
		$endTime = $value['endTime'] ?? null;
		$form = CmsFactory::view()->fragment()->form(['class'=>'form-basic form-action']);
		$form->action("/admin/action/$case")->method('post');
		if ($case == 'edit') {
			$typeBuilder = new TypeBuilder('action', $value);
			$idaction = $typeBuilder->getId();
			$form->input('idaction', (string) $idaction, 'hiddern');
		}
		// THING
		$form = Thing::formContent($form, $value);
		// AGENT
		$form->content(CmsFactory::view()->fragment()->reactShell('organization')->getItemType(_("Agent"), 'agent', $agent)->ready());
		// OBJECT
		$form->content(CmsFactory::view()->fragment()->reactShell('certification')->getItemType(_("Object"), 'object', $object)->ready());
		// PARTICIPANT
		$form->content(CmsFactory::view()->fragment()->reactShell('person')->getItemType(_("participant"), 'participant', $participant)->ready());
		// START TIME
		$form->fieldsetWithInput('startDate', $startTime, _("Start time"), 'datetime-local');
		// END TIME
		$form->fieldsetWithInput('endDate', $endTime, _("End time"), 'datetime-local');
		// SUBMIT
		if ($case == 'edit') {
			$form->submitButtonDelete('/admin/action/delete');
		}
		$form->submitButtonSend();
		// READY
		return $form->ready();
	}
}