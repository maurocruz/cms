<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Event;

use Exception;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

class EventView extends EventAbstract
{
    use FormElementsTrait;

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

        View::main(self::listAll($data, "event", _("List of events"), [ "startDate" => _("Date") ]));
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
            View::main(Fragment::box()->expandingBox(_("Place"), self::relationshipOneToOne("event", $this->idEvent, "location", "place", $value['location'])));

            // IMAGE
            View::main(Fragment::box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("event", $this->idEvent, $value['image'])));
        }
    }
}
