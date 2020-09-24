<?php

namespace Plinct\Cms;

use Slim\App as Slim;

class App 
{
    private $slim;
    private static $HostAPI;
    private static $TITLE;
    private static $LANGUAGE;
    private static $TypesEnabled;

    public function __construct(Slim $slim) {
        $this->slim = $slim;
        self::$HostAPI = "//" . $_SERVER['HTTP_HOST']. "/api";
    }

    public function setLanguage($language) { self::$LANGUAGE = $language; return $this; }
    public function setTitle($title) { self::$TITLE = $title; return $this; }
    public function setTypesEnabled(array $types) { self::$TypesEnabled = $types; return $this; }
    public function defineStaticFilesFolder(string $folderPath) { define('FOLDER_PATH', $folderPath); return $this; }

    public static function getLanguage() { return self::$LANGUAGE; }
    public static function getTitle() { return self::$TITLE; }
    public static function getTypesEnabled() { return self::$TypesEnabled; }
    public static function getHostAPI() { return self::$HostAPI; }


    public function run() 
    {
        $route = include __DIR__ . '/routes/routes.php';
        return $route($this->slim);
    }
}
