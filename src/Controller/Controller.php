<?php
namespace Plinct\Cms\Controller;

class Controller
{
    public function getData($type, $action, $params) {
        $controlClassName = "\\Plinct\\Cms\\Controller\\".ucfirst($type)."Controller";
        if (class_exists($controlClassName)) {
            return (new $controlClassName())->{$action}($params);
        }
        return null;
    }
}