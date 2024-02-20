<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Event;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class Event extends EventAbstract implements TypeInterface
{
  /**
   *
   */
  protected function navbarEvent(): void
  {
		CmsFactory::view()->addHeader(
	    CmsFactory::view()->fragment()->navbar(_("Events"), [
	      "/admin/event" => CmsFactory::view()->fragment()->icon()->home(),
	      "/admin/event/new" => CmsFactory::view()->fragment()->icon()->plus()
	    ], 3, ["table"=>"event"])->ready()
		);
  }

	/**
	 * @param array|null $value
	 */
  public function index(?array $value): void
  {
    // NAVBAR
    $this->navbarEvent();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('Event')->setColumnsTable(["startDate"=>"Início"])->ready()
		);
  }

  /**
   *
   * @param array|null $value
   */
  public function new(?array $value): void
  {
    // NAVBAR
    $this->navbarEvent();
    // FORM
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(parent::formEvent(), _("Add new")));
  }

  /**
   * @throws Exception
   */
  public function edit(?array $data): void
  {
    // NAVBAR
    $this->navbarEvent();
    if (!$data) {
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->miscellaneous()->message(_("Event not found")));
    } else {
      $value = $data[0];
      $this->idevent = $value['idevent'];
      // VIEW IN SITE
      CmsFactory::view()->addMain([ "tag" => "p", "content" => _("View on website"), "href" => "/eventos/". substr($value['startDate'], 0, 10)."/". urlencode($value['name']), "hrefAttributes" => [ "target" => "_blank" ] ]);
      // EVENT FORM
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formEvent('edit', $value), _("Edit event")));
			// SUPER EVENTS
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Super Event"), CmsFactory::view()->fragment()->form()->relationship("event", (string)$this->idevent, "event")->oneToOne('superEvent', $value['superEvent'], 'startDate desc') ));
			// SUB EVENTS
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Sub Events"), CmsFactory::view()->fragment()->form()->relationshipOneToMany("event", (string) $this->idevent, 'event', $value['subEvent'], "idevent desc")));
      // PLACE
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Place"), CmsFactory::view()->fragment()->form()->relationship("event", (string) $this->idevent, "place")->oneToOne("location", $value['location'], "dateCreated")));
      // IMAGE
	    CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->reactShell('imageObject')->setTableHasPart('event')->setIdHasPart($value['idevent'])->ready()
	    );
    }
  }

	public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array
	{
		return [];
	}
}
