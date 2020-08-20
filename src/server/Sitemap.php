<?php

namespace Plinct\Cms\Server;

class Sitemap {

    public static function create($type)
    {
        $classController = "\\Plinct\\Cms\\Controller\\" . ucfirst($type) . "Controller";

        if (class_exists($classController)) {
            $objectController = new $classController();

            if (method_exists($objectController, "saveSitemap")) {
                $objectController->saveSitemap();
            }
        }
    }
}
