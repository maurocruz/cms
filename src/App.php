<?php
namespace Plinct\Cms;

use Plinct\Cms\Server\Api;
use Slim\App as Slim;

class App {
    private static $IMAGES_FOLDER;
    private $slim;
    private static $TITLE;
    private static $LANGUAGE;
    public static $TypesEnabled;
    private static $VERSION;
    public static $HOST;
    public static $API_HOST;

    public function __construct(Slim $slim) {
        $this->slim = $slim;
        self::$HOST = (filter_input(INPUT_SERVER, 'HTTPS') == 'on' ? "https" : "http") . ":" . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . filter_input(INPUT_SERVER,'HTTP_HOST');
        self::setVersion();
    }

    public function setApiHost(string $absoluteUrl) {
        self::$API_HOST = $absoluteUrl;
        Api::setApiHost($absoluteUrl);
    }
    public function setLanguage($language): App {
        self::$LANGUAGE = $language; return $this;
    }
    public function setTitle($title): App {
        self::$TITLE = $title; return $this;
    }
    public function setTypesEnabled(array $types): App {
        self::$TypesEnabled = $types; return $this;
    }
    public function defineStaticFilesFolder(string $folderPath): App {
        define('FOLDER_PATH', $folderPath); return $this;
    }
    public function setImagesFolder($relativePath): App {
        self::$IMAGES_FOLDER = $_SERVER['DOCUMENT_ROOT'] . $relativePath; return $this;
    }
    public static function setVersion() {
        $version = "developer version";
        $installedFile = realpath($_SERVER['DOCUMENT_ROOT'] . "/../vendor/composer/installed.json");
        $packages = json_decode(file_get_contents($installedFile));
        foreach ($packages->packages as $package) {
            if ($package->name && $package->name == "plinct/cms") {
                $version = $package->version;
            }
        }
        self::$VERSION = $version;
    }
    public static function getImagesFolder() {
        return self::$IMAGES_FOLDER;
    }
    public static function getLanguage() {
        return self::$LANGUAGE;
    }
    public static function getTitle() {
        return self::$TITLE;
    }
    public static function getTypesEnabled() {
        return self::$TypesEnabled;
    }
    public static function getVersion() {
        return self::$VERSION;
    }
    public function run() {
        $route = include __DIR__ . '/routes/routes.php';
        return $route($this->slim);
    }
}
