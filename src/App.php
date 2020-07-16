<?php

namespace Plinct\Cms;

use Slim\App as Slim;

class App 
{
    private $slim;
    
    private static $TITLE;
    
    private static $LANGUAGE;
    
    private static $TypesEnabled;

    public function __construct(Slim $slim)
    {
        $this->slim = $slim;
    }
    
    public function setTypesEnabled(array $types)
    {
        self::$TypesEnabled = $types;
    }
    
    public static function getTypesEnabled()
    {
        return self::$TypesEnabled;
    }
    
    public function setTitle($title)
    {
        self::$TITLE = $title;
    }
    
    public static function getTitle()
    {
        return self::$TITLE;
    }
    
    public function setLanguage($language)
    {
        self::$LANGUAGE = $language;
    }
    
    public static function getLanguage()
    {
        return self::$LANGUAGE;
    }

    public function run() 
    {
        $route = include __DIR__ . '/routes/routes.php';
        return $route($this->slim);
    }
}
