<?php
namespace Plinct\Cms;

use Slim\App as Slim;

class CmsFactory
{    
    public static function create(Slim $slim): App {
        return new App($slim);
    }  
}
