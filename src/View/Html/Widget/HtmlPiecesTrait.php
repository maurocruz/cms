<?php

namespace Plinct\Cms\View\Html\Widget;

trait HtmlPiecesTrait
{
    protected static function error($data, $type)
    {
        if ($data['code'] == '1146') {
            return [ "tag" => "div", "content" => [
                [ "tag" => "p", "content" => _($data['message']) ],
                [ "tag" => "form", "attributes" => [ "action" => "/admin/$type/createSqlTable", "method" => "post" ], "content" => [
                    [ "tag" => "input", "attributes" => [ "type" => "submit", "value" => _("Do you want to create it?"), "style" => "cursor: pointer;" ] ]
                ]]
            ]];
        } else {
            return [ "tag" => "p", "attributes" => [ "class" => "warning"], "content" => $data['message'] ];
        }
    }
}