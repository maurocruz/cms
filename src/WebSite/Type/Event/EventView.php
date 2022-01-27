<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Event;

use Exception;
use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Tool\ArrayTool;

class EventView extends EventAbstract
{
    /**
     *
     */
    protected function navbarEvent()
    {
        View::navbar(_("Events"), [
            "/admin/event" => Fragment::icon()->home(),
            "/admin/event/new" => Fragment::icon()->plus()
        ], 3, ["table"=>"event"]);
    }

    /**
     * @param array $data
     */
    public function index(array $data)
    {
        // NAVBAR
        $this->navbarEvent();

        $tablelIst = Fragment::listTable()
            ->caption(_("List of events"))
            ->labels(_('Name'),_("Date"))
            ->rows($data['itemListElement'],['name','startDate'])
            ->setEditButton("/admin/event/edit/");
        View::main($tablelIst->ready());
    }

    /**
     *
     */
    public function new()
    {
        // NAVBAR
        $this->navbarEvent();

        // FORM
        View::main(Fragment::box()->simpleBox(parent::formEvent(), _("Add new")));
    }

    /**
     * @throws Exception
     */
    public function edit(array $data)
    {
        // NAVBAR
        $this->navbarEvent();

        if (!$data) {
            View::main(Fragment::miscellaneous()->message(_("Event not found")));

        } else {
            $value = $data[0];
            $this->idEvent = (int)ArrayTool::searchByValue($value['identifier'], 'id')['value'];

            // VIEW IN SITE
            View::main([ "tag" => "p", "content" => _("View on website"), "href" => "/eventos/". substr($value['startDate'], 0, 10)."/". urlencode($value['name']), "hrefAttributes" => [ "target" => "_blank" ] ]);

            // EVENT FORM
            View::main(Fragment::box()->simpleBox(self::formEvent('edit', $value), _("Edit event")));

            // PLACE
            View::main(Fragment::box()->expandingBox(_("Place"), Fragment::form()->relationshipOneToOne("event", (string) $this->idEvent, "location", "place", $value['location'])));

            // IMAGE
            View::main(Fragment::box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("event", $this->idEvent, $value['image'])));
        }
    }
}
