<?php
namespace Plinct\Cms\View\WebSite\Type\Event;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;

abstract class EventAbstract
{
  /**
   * @var ?int
   */
  protected ?int $idevent = null;

	protected function navbarEvent(): void
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('event')
				->title(_("Events"))
				->newTab("/admin/event", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/event/new", CmsFactory::view()->fragment()->icon()->plus())
				->search()
				->ready()
		);
	}

  /**
   * @param string $case
   * @param array|null $value
   * @return array
   */
  protected function formEvent(string $case = "new", array $value = null): array
  {
    // VARS
		$startDate = isset($value['startDate']) ? substr($value['startDate'],0,10) : null;
		$startTime = isset($value['startDate']) ? substr($value['startDate'],11) : null;
		$endDate = isset($value['endDate']) ? substr($value['endDate'],0,10) : null;
		$endTime = isset($value['endDate']) ? substr($value['endDate'],11) : null;

		$organizer = $value['organizer'] ?? null;
		$location = $value['location'] ?? null;
		$superEvent = $value['superEvent'] ?? null;
		$typeLocation = CmsFactory::toolBox()::typeBuilder($location);
		$superEventTb = $superEvent ? CmsFactory::toolBox()::typeBuilder($superEvent) : null;
    // FROM
    $form = CmsFactory::view()->fragment()->form("form-event",["class"=>"form-basic form-event"]);
    $form->action("/admin/event/$case")->method("post");
		$form->setIdform("form-event-".($this->idevent ?? "new"));
    // HIDDENS
    if ($case == "edit") {
			$form->input('idevent', (string)$this->idevent, 'hidden');
    }
		// THING
		$form = Thing::formContent($form, $value);
    // START DATE
    $form->fieldsetWithInput('startDate', $startDate, _("Start date"), "date");
    $form->fieldsetWithInput('startTime', $startTime, _("Start time"), "time");
    // END DATE
    $form->fieldsetWithInput('endDate', $endDate, _("End date"), "date");
    $form->fieldsetWithInput('endTime', $endTime, _("End time"), "time");
		// LOCATION
	  $form->relationshipOneToOne('place', _('Place'),'location', $typeLocation->getId());
		// ORGANIZER
	  $form->relationshipOneToOne('organization,person',_('Organizer'),'organizer',$organizer);
		// SUPER EVENT
	  $form->relationshipOneToOne('event',_('Super event'),'superEvent', $superEvent ? $superEventTb->getIdthing(): null);
    // BUTTONS
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete("/admin/event/erase");
    // READY
    return $form->ready();
  }
}
