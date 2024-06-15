<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Event;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;

abstract class EventAbstract
{
  /**
   * @var ?int
   */
  protected ?int $idevent = null;

  /**
   * @param string $case
   * @param array|null $value
   * @return array
   */
  protected function formEvent(string $case = "new", array $value = null): array
  {
    // VARS
    $startDate = $value['startDate'] ?? null;
    $endDate = $value['endDate'] ??  null;
		$organizer = $value['organizer'] ?? null;
		$location = $value['location'] ?? null;
    // FROM
    $form = CmsFactory::view()->fragment()->form(["class"=>"form-basic form-event"]);
    $form->action("/admin/event/$case")->method("post");
    // HIDDENS
    if ($case == "edit") {
			$form->input('idevent', (string)$this->idevent, 'hidden');
    }
		// THING
		$form = Thing::formContent($form, $value);
    // START DATE
    $form->fieldsetWithInput('startDate', $startDate, _("Start date"), "datetime-local");
    // END DATE
    $form->fieldsetWithInput('endDate', $endDate, _("End date"), "datetime-local");
		// LOCATION
	  $form->relationshipOneToOne('place', _('Place'),'location', $location);
		// ORGANIZER
	  $form->relationshipOneToOne('thing',_('Organizer'),'organizer',$organizer);
    // BUTTONS
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete("/admin/event/erase");
    // READY
    return $form->ready();
  }
}
