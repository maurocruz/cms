<?php
namespace Plinct\Cms;

use Slim\App as Slim;

class CmsFactory {
    /**
     * @param Slim $slim
     * @return App
     */
    public static function create(Slim $slim): App {
        return new App($slim);
    }

}
