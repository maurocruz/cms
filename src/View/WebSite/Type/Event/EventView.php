<?php
namespace Plinct\Cms\View\WebSite\Type\Event;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class EventView extends ThingView implements TypeViewInterface
{
	/**
	 * @var ?int
	 */
	protected ?int $idevent = null;

	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'event', string $sitemapFilename = 'sitemap-event.xml')
	{
		$this->sitemapExtension = 'news';
		parent::__construct($type, $sitemapFilename);
	}

	/**
	 *
	 */
	public function __destruct() {
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('event')
				->title(_("Events"))
				->newTab("/admin/event", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/event/new", CmsFactory::view()->fragment()->icon()->plus())
				->newTab("/admin/event/sitemap", CmsFactory::view()->fragment()->icon()->sitemap())
				->search()
				->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 */
  public function index(?array $data, array $queryParams = null): void
  {
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('event')->setColumnsTable(["startDate"=>"Início"])->ready()
		);
  }

  /**
   *
   * @param array|null $data
   * @param array|null $queryParams
   */
  public function new(?array $data, array $queryParams = null): void
  {
    // FORM
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formEvent(), _("Add new")));
  }

  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @throws Exception
   */
  public function edit(?array $data, array $queryParams = null): void
  {
    if (!$data) {
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->miscellaneous()->message(_("Event not found")));
    } else {
      $value = $data[0];
			$typeBuilder = CmsFactory::toolBox()::typeBuilder($value);
      $this->idevent = $typeBuilder->getId();
			$idthing = $typeBuilder->getIdthing();
      // EVENT FORM
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formEvent('edit', $value), _("Edit event")));
			// ADDITIONAL TYPES
	    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('event')->setProperty('additionalType')->setIdHasPart($idthing)->ready());
			// SUB EVENTS
      CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(
					_("Sub Events"),
					CmsFactory::view()->fragment()->form("form-event")->relationshipOneToMany("event", $idthing, 'event', $value['subEvent'] ?? null, "startDate")
				)
      );
      // IMAGE
	    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart($idthing)->ready());
    }
  }

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	protected function formEvent(string $case = "new", array $value = null): array
	{
		// VARS
		$startDate = isset($value['startDate']) ? substr($value['startDate'], 0, 10) : null;
		$startTime = isset($value['startDate']) ? substr($value['startDate'], 11) : null;
		$endDate = isset($value['endDate']) ? substr($value['endDate'], 0, 10) : null;
		$endTime = isset($value['endDate']) ? substr($value['endDate'], 11) : null;
		$location = $value['location'] ?? null;
		$organizer = $value['organizer'] ?? null;
		$superEvent = $value['superEvent'] ?? null;
		// FROM
		$form = CmsFactory::view()->fragment()->form("form-event",["class"=>"form-basic form-event"]);
		$form->action("/admin/event/$case")->method("post");
		$form->setIdform("form-event-".($this->idevent ?? "new"));
		$form->addMandatories('startDate','endDate','location');
		// HIDDENS
		if ($case == "edit") {
			$form->input('idevent', (string)$this->idevent, 'hidden');
		}
		// THING
		$form = ThingView::formThingContent($form, $value);
		// START DATE
		$form->fieldsetWithInput('startDate', $startDate, _("Start date"), "date");
		$form->fieldsetWithInput('startTime', $startTime, _("Start time"), "time");
		// END DATE
		$form->fieldsetWithInput('endDate', $endDate, _("End date"), "date");
		$form->fieldsetWithInput('endTime', $endTime, _("End time"), "time");
		// LOCATION
		$form->relationshipOneToOne('place', _('Place'),'location', $location);
		// ORGANIZER
		$form->relationshipOneToOne('organization,person',_('Organizer'),'organizer',$organizer);
		// SUPER EVENT
		$form->relationshipOneToOne('event',_('Super event'),'superEvent', $superEvent);
		// BUTTONS
		$form->submitButtonSend();
		if ($case == "edit") $form->submitButtonDelete("/admin/event/erase");
		// READY
		return $form->ready();
	}
}
