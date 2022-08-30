<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Event;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;

class EventView extends EventAbstract
{
  /**
   *
   */
  protected function navbarEvent()
  {
    CmsFactory::webSite()->navbar(_("Events"), [
      "/admin/event" => CmsFactory::response()->fragment()->icon()->home(),
      "/admin/event/new" => CmsFactory::response()->fragment()->icon()->plus()
    ], 3, ["table"=>"event"]);
  }

  /**
   * @param array $data
   */
  public function index(array $data)
  {
    // NAVBAR
    $this->navbarEvent();

    $tablelIst = CmsFactory::response()->fragment()->listTable()
      ->caption(_("List of events"))
      ->labels(_('Name'),_("Date"))
      ->rows($data['itemListElement'],['name','startDate'])
      ->setEditButton("/admin/event/edit/");
    CmsFactory::webSite()->addMain($tablelIst->ready());
  }

  /**
   *
   */
  public function new()
  {
    // NAVBAR
    $this->navbarEvent();
    // FORM
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(parent::formEvent(), _("Add new")));
  }

  /**
   * @throws Exception
   */
  public function edit(array $data)
  {
    // NAVBAR
    $this->navbarEvent();

    if (!$data) {
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->miscellaneous()->message(_("Event not found")));

    } else {
      $value = $data[0];
      $this->idevent = $value['idevent'];
      // VIEW IN SITE
      CmsFactory::webSite()->addMain([ "tag" => "p", "content" => _("View on website"), "href" => "/eventos/". substr($value['startDate'], 0, 10)."/". urlencode($value['name']), "hrefAttributes" => [ "target" => "_blank" ] ]);
      // EVENT FORM
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(self::formEvent('edit', $value), _("Edit event")));
			// SUPER EVENTS
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Super Event"), CmsFactory::response()->fragment()->form()->relationship("event", $this->idevent, "event")->oneToOne('superEvent', $value['superEvent'], 'startDate desc') ));
			// SUB EVENTS
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Sub Events"), CmsFactory::response()->fragment()->form()->relationshipOneToMany("event", $this->idevent, 'event', $value['subEvent'], "idevent desc")));
      // PLACE
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Place"), CmsFactory::response()->fragment()->form()->relationship("event", $this->idevent, "place")->oneToOne("location", $value['location'], "dateCreated")));
      // IMAGE
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("event", $this->idevent, $value['image'])));
    }
  }
}
