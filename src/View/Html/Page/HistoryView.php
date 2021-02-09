<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Tool\DateTime;

class HistoryView
{    
    public function view($data): array
    {
        $body = null;
        if (isset($data['message']) && $data['message'] == "No data founded") {
            $body[] = [ "tag" => "tr", "content" => [ 
                [ "tag" => "td", "attributes" => [ "colspan" => "4" ], "content" => "Não há registros!" ] ] 
            ];
        } else {
            foreach ($data as $value) {
                $body[] = [ "tag" => "tr", "content" => [
                    [ "tag" => "td", "content" => DateTime::formatDateTime($value['datetime']) ],
                    [ "tag" => "td", "content" => $value['action'] ],
                    [ "tag" => "td", "content" => stripslashes($value['summary']) ],
                    [ "tag" => "td", "content" => $value['user'] ]
                ]];
            }
        }
        
        return [ "tag" => "div", "attributes" => [ "class" => "box"], "content" => [
            [ "tag" => "h4", "content" => _("History") ],
            [ "tag" => "table", "attributes" => [ "class" => "contrato-table--history box " ], "content" => [
                [ "tag" => "thead", "content" => [
                    [ "tag" => "tr", "content" => [
                        [ "tag" => "th", "attributes" => [ "style" => "width: 160px;" ], "content" => _("Date") ],
                        [ "tag" => "th", "attributes" => [ "style" => "width: 80px;" ], "content" => _("Action") ],
                        [ "tag" => "th", "content" => _("Summary") ],
                        [ "tag" => "th", "attributes" => [ "style" => "width: 150px;" ], "content" => _("Author") ]
                    ]]
                ]],
                [ "tag" => "tbody", "content" => $body ]
            ]]
        ]];
    }
}
