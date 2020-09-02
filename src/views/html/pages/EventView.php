<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Web\Widget\FormTrait;
use Plinct\Api\Type\PropertyValue;

class EventView
{
    protected $content;
    
    use FormTrait;
    
    protected function navbar() 
    {        
        $this->content['navbar'][] = [
            "list" => [ "/admin/event" => _("View all"), "/admin/event/add" => _("Add new event") ],
            "attributes" => [ "class" => "menu menu3" ],
            "title" => _("Events")            
        ];
    }
    
    public function index(array $data): array 
    {
        $this->navbar();
        
        $this->content['main'][] = self::listAll($data, "event", _("List of events"), [ "startDate" => _("Date") ]);
        
        return $this->content;
    }
        
    public function add($data = null): array
    {
        $this->navbar();
        
        $this->content['main'][] = [ "tag" => "div", "attributes" => [ "class" => "events" ], "content" => [
            [ "tag" => "h1", "content" => _("Add") ],
            self::form()
        ]];
        
        return $this->content;
    }
        
    public function edit(array $data): array 
    {            
        $this->navbar();
        
        if (!$data) {
            $content[] = [ "tag" => "p", "attributes" => [ "class" => "aviso" ], "content" => _("Event not found") ];            
        } else {
            $value = $data[0];

            $ID = PropertyValue::extractValue($value['identifier'], 'id');

            $content[] = [ "tag" => "p", "content" => _("View on website"), "href" => "/eventos/". substr($value['startDate'], 0, 10)."/". urlencode($value['name']), "hrefAttributes" => [ "target" => "_blank" ] ];

            // event 
            $content[] = self::form('edit', $value);

            // place
            $content[] = self::divBoxExpanding(_("Place"), "place", [(new PlaceView())->getForm("event", $ID, $value['location']) ]);

            // images
            $content[] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("event", $ID, $value['image']) ]);;
        }        
        $this->content['main'][] = self::wrapper($content);
        
        return $this->content;
    }
    
    private static function wrapper($content) 
    {
        return [ "tag" => "div", "attributes" => [ "class" => "events" ], "content" => $content ];
    }

    /*
     * Formulário de edição dos dados do evento
     */
    private static function form($case = 'add', $value = null) 
    {
        $startDate = strstr($value['startDate'], " ", true); 
        $startTime = substr(strstr($value['startDate'], " "),1);

        $endDate = strstr($value['endDate'], " ", true); 
        $endTime = substr(strstr($value['endDate'], " "),1);

        $content[] = [ "tag"=>"h4", "content" => _("Event") ];

        if ($case == "edit") {
            $ID = PropertyValue::extractValue($value['identifier'], 'id');
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $ID ?? null ] ];
        }

        // title
        $content[] = [ "tag" => "fieldset", "attributes" => ["style" => "width: 100%;"], "content" => [ 
            [ "tag" =>"legend", "content" => _("Title") ],
            [ "tag" => "input", "attributes" => [ "name"=>"name", "value"=>$value['name'] ] ] 
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
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", stripslashes($value['description']));

        // submit
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/event/erase") : null;

        // form
        return [ "tag"=>"form", "attributes"=> [ "name" => "event-form--{$case}", "id" => 'event-form', "class"=>"box formPadrao", "enctype"=>"multipart/form-data", "method"=>"post", "action" => "/admin/event/$case" ], "content" => $content ];
    }
}