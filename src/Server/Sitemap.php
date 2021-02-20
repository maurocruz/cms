<?php
namespace Plinct\Cms\Server;

class Sitemap
{
    public static function create($type, $params = null) {
        $classController = "\\Plinct\\Cms\\Controller\\" . ucfirst($type) . "Controller";
        if (class_exists($classController)) {
            $objectController = new $classController();
            if (method_exists($objectController, "saveSitemap")) {
                $objectController->saveSitemap($params);
            } else {
                die('Method '.$classController.'::saveSitemap() not exists');
            }
        }
    }
}
