<?php
namespace Plinct\Cms\View;

class View {
    public function view($type, $action, $data): array {
        $viewClassName = "\\Plinct\\Cms\\View\\Html\\Page\\".ucfirst($type)."View";
        if (class_exists($viewClassName)) {
            // ERROR
            if (isset($data['error']))
                return [ "main" => [ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => $data['error']['message'] ]];
            // VIEW
            $view = (new $viewClassName())->{$action}($data);
            return [ "navbar" => $view['navbar'], "main" => $view['main'] ];
        }
        // TYPE NOT FOUND
        return [ "navbar" => [], "main" => [ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => _("$type type view not founded") ] ];
    }
}