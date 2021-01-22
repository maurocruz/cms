<?php

namespace Plinct\Cms\View\Html\Widget;

trait navbarTrait 
{
    public static function navbar(string $title, array $list = [], $level = 2, $appendNavbar = null): array
    {
        return [ "list" => $list, "attributes" => [ "class" => "menu menu$level" ], "title" => _($title), "append" => $appendNavbar ];
    }

    public static function searchPopupList(string $table, string $property = "name", string $params = ""): array
    {
        return [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => $table, "data-like" => $property, "data-params" => $params  ] ];
    }
}
