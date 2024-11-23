<?php
declare(strict_types=1);
namespace Plinct\Cms\WebSite\Type\Event;

use Exception;
use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;

class EventView extends EventAbstract
{
  /**
   *
   */
  protected function navbarEvent(): void
  {
    CmsFactory::webSite()->navbar(_("Events"), [
      "/admin/event" => CmsFactory::response()->fragment()->icon()->home(16,16),
      "/admin/event/new" => CmsFactory::response()->fragment()->icon()->plus(16,16)
    ], 3, ["table"=>"event"]);
  }

	/**
	 */
  public function index(): void
  {
    // NAVBAR
    $this->navbarEvent();
		CmsFactory::webSite()->addMain("
			<div 
				class='plinct-shell' 
				data-type='Event'
				data-apihost='".App::getApiHost()."' 
				data-usertoken='".CmsFactory::request()->user()->userLogged()->getToken()."'
				data-columnsTable='{\"edit\":\"Edit\",\"name\":\"Nome\",\"startDate\":\"Início\",\"dateModified\":\"Modificado\"}'
			></div>");
  }

  /**
   *
   */
  public function new(): void
  {
    // NAVBAR
    $this->navbarEvent();
    // FORM
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(parent::formEvent(), _("Add new")));
  }

  /**
   * @throws Exception
   */
  public function edit(array $data): void
  {
    // NAVBAR
    $this->navbarEvent();

    if (!$data) {
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->miscellaneous()->message(_("Event not found")));

    } else {
      $value = $data[0];
      $this->idevent = (string) $value['idevent'];
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
	    CmsFactory::webSite()->addMain("
				<div
					class='plinct-shell'
					data-type='imageObject'
					data-tablehaspart='event'
					data-idhaspart='".$value['idevent']."'
					data-apihost='".App::getApiHost()."'
					data-usertoken='".CmsFactory::request()->user()->userLogged()->getToken()."'
				></div>");
      //CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("event", $this->idevent, $value['image'])));
    }
  }
}
