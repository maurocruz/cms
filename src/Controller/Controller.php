<?php
namespace Plinct\Cms\Controller;


class Controller {

    public function getData($type, $action, $params) {
        $controlClassName = "\\Plinct\\Cms\\Controller\\".ucfirst($type)."Controller";
        if (class_exists($controlClassName)) {
            return (new $controlClassName())->{$action}($params);
        }
        return null;
            /*$viewClassName = "\\Plinct\\Cms\\View\\Html\\Page\\".ucfirst($type)."View";
            if (class_exists($viewClassName)) {
                if(isset($controlData['message']) && $controlData['message'] == "No data founded") {
                    $viewData['main'][] = (new $viewClassName())->noContent();
                } else {
                    $viewData = (new $viewClassName())->{$action}($controlData);
                }
                // navbar
                if (array_key_exists('navbar', $viewData)) {
                    foreach ($viewData['navbar'] as $value) {
                        //parent::addNavBar($value);
                    }
                }
                // main
                if (array_key_exists('main', $viewData)) {
                    //parent::addMain($viewData['main']);
                }
            } else {
                //parent::addMain([ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => "$type type view not founded" ]);
            }
        } else {
            //parent::addMain([ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => "$type type not founded" ]);
        }*/
    }
}