<?php
namespace Plinct\Cms\Controller;

class Controller
{
    public function getData($type, $action, $params) {
        $controlClassName = "\\Plinct\\Cms\\Controller\\".ucfirst($type)."Controller";
        if (class_exists($controlClassName)) {
            $object = new $controlClassName();
            if (method_exists($object, $action)) {
                return $object->{$action}($params);
            }
        }
        return null;
    }
}