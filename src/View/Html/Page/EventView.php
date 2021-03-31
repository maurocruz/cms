<?php
namespace Plinct\Cms\View\Html\Page;

use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;
use Plinct\Api\Type\PropertyValue;

class EventView {
    protected $content;
    
    use FormElementsTrait;
    use navbarTrait;
    
    protected function navbarEvent() {
        $title = _("Events");
        $list = [ "/admin/event" => _("View all"), "/admin/event/new" => _("Add new") ];
        $level = 3;
        $appendNavbar = [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "event", "data-searchfor" => "name" ] ];
        $this->content['navbar'][] = self::navbar($title, $list, $level, $appendNavbar);
    }
    
    public function index(array $data): array {
        $this->navbarEvent();
        $this->content['main'][] = self::listAll($data, "event", _("List of events"), [ "startDate" => _("Date") ]);
        return $this->content;
    }
        
    public function new(): array {
        $this->navbarEvent();
        $this->content['main'][] = self::div(_("Add new"), "event", [ self::formEvent() ]);
        return $this->content;
    }
        
    public function edit(array $data): array {
        $this->navbarEvent();
        if (!$data) {
            $content[] = [ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => _("Event not found") ];            
        } else {
            $value = $data[0];
            $idEvent = PropertyValue::extractValue($value['identifier'], 'id');
            $content[] = [ "tag" => "p", "content" => _("View on website"), "href" => "/eventos/". substr($value['startDate'], 0, 10)."/". urlencode($value['name']), "hrefAttributes" => [ "target" => "_blank" ] ];
            // event 
            $content[] = self::formEvent('edit', $value);
            // place
            $content[] = self::divBoxExpanding(_("Place"), "place", [ self::relationshipOneToOne("event", $idEvent, "location", "place", $value['location']) ]);
            // images
            $content[] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("event", $idEvent, $value['image']) ]);
        }
        $this->content['main'][] = [ "tag" => "div", "attributes" => [ "class" => "events" ], "content" => $content ];
        return $this->content;
    }

    /*
     * Formulário de edição dos dados do evento
     */
    private static function formEvent($case = 'new', $value = null): array {
        $startDate = isset($value) ? strstr($value['startDate'], " ", true) : null;
        $startTime = isset($value) ? substr(strstr($value['startDate'], " "), 1) : null;
        $endDate = isset($value) ? strstr($value['endDate'], " ", true) : null;
        $endTime = isset($value) ? substr(strstr($value['endDate'], " "), 1) : null;
        $content[] = [ "tag"=>"h4", "content" => _("Event") ];
        if ($case == "edit") {
            $ID = PropertyValue::extractValue($value['identifier'], 'id');
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
        // description
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", stripslashes($value['description'] ?? null));
        // submit
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/event/erase") : null;
        // form
        return [ "tag"=>"form", "attributes"=> [ "name" => "event-form--{$case}", "id" => 'event-form', "class"=>"box formPadrao", "enctype"=>"multipart/form-data", "method"=>"post", "action" => "/admin/event/$case" ], "content" => $content ];
    }
}
