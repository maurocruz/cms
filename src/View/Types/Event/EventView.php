<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Event;

use Exception;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

class EventView
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
        $this->navbarEvent();
        View::main(self::listAll($data, "event", _("List of events"), [ "startDate" => _("Date") ]));
    }

    /**
     *
     */
    public function new()
    {
        $this->navbarEvent();
        View::main(self::div(_("Add new"), "event", [ self::formEvent() ]));
    }

    /**
     * @throws Exception
     */
    public function edit(array $data)
    {
        $this->navbarEvent();
        if (!$data) {
            $content[] = [ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => _("Event not found") ];            
        } else {
            $value = $data[0];
            $idEvent = (int)ArrayTool::searchByValue($value['identifier'], 'id')['value'];
            $content[] = [ "tag" => "p", "content" => _("View on website"), "href" => "/eventos/". substr($value['startDate'], 0, 10)."/". urlencode($value['name']), "hrefAttributes" => [ "target" => "_blank" ] ];
            // event 
            $content[] = self::formEvent('edit', $value);
            // place
            $content[] = self::divBoxExpanding(_("Place"), "place", [ self::relationshipOneToOne("event", $idEvent, "location", "place", $value['location']) ]);
            // images
            $content[] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("event", $idEvent, $value['image']) ]);
        }
        View::main([ "tag" => "div", "attributes" => [ "class" => "events" ], "content" => $content ]);
    }

    /**
     *  Formulário de edição dos dados do evento
     * @param string $case
     * @param null $value
     * @return array
     */
    private static function formEvent(string $case = 'new', $value = null): array
    {
        $startDate = isset($value) ? strstr($value['startDate'], " ", true) : null;
        $startTime = isset($value) ? substr(strstr($value['startDate'], " "), 1) : null;
        $endDate = isset($value) ? strstr($value['endDate'], " ", true) : null;
        $endTime = isset($value) ? substr(strstr($value['endDate'], " "), 1) : null;
        $description = isset($value['description']) ? stripslashes($value['description']) : null;

        $content[] = [ "tag"=>"h4", "content" => _("Event") ];

        if ($case == "edit") {
            $ID = ArrayTool::searchByValue($value['identifier'], 'id')['value'];
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $ID ?? null ] ];
        }
        // title
        $content[] = [ "tag" => "fieldset", "attributes" => ["style" => "width: 100%;"], "content" => [ 
            [ "tag" =>"legend", "content" => _("Title") ],
            [ "tag" => "input", "attributes" => [ "name"=>"name", "value"=>$value['name'] ?? null ] ]
        ]];
        // startDate
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" => "legend", "content" => _("Start date") ],
            [ "tag" => "input", "attributes" => [ "name"=>"startDate", "type" => "date", "value" => $startDate ] ] 
        ]];
        // startTime
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" => "legend", "content" => _("Start time") ],
            [ "tag" => "input", "attributes" => [ "name"=>"startTime", "type" => "time", "value" => $startTime ] ]
        ]];
        // endDate
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" => "legend", "content" => _("End date") ],
            [ "tag" => "input", "attributes" => [ "name"=>"endDate", "type" => "date", "value "=> $endDate ] ] 
        ]];
        // endTime
        $content[] = [ "tag" => "fieldset", "content" => [ 
            [ "tag" => "legend", "content" => _("End time") ],
            [ "tag" => "input", "attributes" => [ "name"=>"endTime", "type" => "time", "value" => $endTime ] ] 
        ]];

        // DESCRIPTION
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $description);

        // submit
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/event/erase") : null;
        // form
        return [ "tag"=>"form", "attributes"=> [ "name" => "event-form--$case", "id" => 'event-form', "class"=>"box formPadrao", "enctype"=>"multipart/form-data", "method"=>"post", "action" => "/admin/event/$case" ], "content" => $content ];
    }
}
