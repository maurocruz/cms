<?php

namespace Plinct\Cms;

use Slim\App as Slim;

class App 
{
    private $slim;
    
    private static $TITLE;

    public function __construct(Slim $slim)
    {
        $this->slim = $slim;
    }
    
    public function setTypesEnebled(array $types)
    {
        
    }
    
    public function setTitle($title)
    {
        self::$TITLE = $title;
    }
    
    public static function getTitle()
    {
        return self::$TITLE;
    }

    public function run() 
    {
        $route = include __DIR__ . '/routes/routes.php';
        return $route($this->slim);
    }
}
