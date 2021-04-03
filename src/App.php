<?php
namespace Plinct\Cms;

use Plinct\Tool\Locale;
use Slim\App as Slim;

/**
 * Class App
 * @package Plinct\Cms
 */
class App {
    private static $IMAGES_FOLDER;
    private $slim;
    private static $TITLE = null;
    private static $LANGUAGE;
    public static $TypesEnabled = [];
    private static $VERSION;
    public static $HOST;
    private static $API_HOST = null;
    private static $API_SECRET_KEY;
    private static $STATIC_FOLDER = "/App/static/cms";

    /**
     * App constructor.
     * @param Slim $slim
     */
    public function __construct(Slim $slim) {
        $this->slim = $slim;
        self::$HOST = (filter_input(INPUT_SERVER, 'HTTPS') == 'on' ? "https" : "http") . ":" . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . filter_input(INPUT_SERVER,'HTTP_HOST');
        self::setVersion();
        self::$LANGUAGE = Locale::getServerLanguage();
    }

    /**
     * API SETTINGS
     * @param string $apiUrl
     * @param string|null $apiSecretKey
     */
    public function setApi(string $apiUrl, ?string $apiSecretKey = null) {
        self::$API_HOST = $apiUrl;
        self::$API_SECRET_KEY = $apiSecretKey;
    }

    /**
     * API HOST GETTING
     * @return string|null
     */
    public static function getApiHost(): ?string {
        return self::$API_HOST;
    }

    /**
     * API SECRET KEY GETTING
     * @return string
     */
    public static function getApiSecretKey(): ?string {
        return self::$API_SECRET_KEY;
    }

    /**
     * GET STATIC FOLDER
     * @return string
     */
    public static function getStaticFolder(): string {
        return self::$STATIC_FOLDER;
    }

    /**
     * SET STATIC FOLDER
     * @param string $STATIC_FOLDER
     * @return App
     */
    public function setStaticFolder(string $STATIC_FOLDER): App {
        self::$STATIC_FOLDER = $STATIC_FOLDER;
        return $this;
    }

    public function setLanguage($language): App {
        self::$LANGUAGE = $language;
        return $this;
    }
    public function setTitle($title): App {
        self::$TITLE = $title; return $this;
    }
    public function setTypesEnabled(array $types): App {
        self::$TypesEnabled = $types; return $this;
    }
    /*public function defineStaticFilesFolder(string $folderPath): App {
        define('FOLDER_PATH', $folderPath); return $this;
    }*/
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

    public static function getImagesFolder(): ?string {
        return self::$IMAGES_FOLDER;
    }
    public static function getLanguage(): string {
        return self::$LANGUAGE;
    }
    public static function getTitle(): ?string {
        return self::$TITLE;
    }
    public static function getTypesEnabled(): array {
        return self::$TypesEnabled;
    }
    public static function getVersion(): string {
        return self::$VERSION;
    }
    public function run() {
        $route = include __DIR__ . '/routes/routes.php';
        return $route($this->slim);
    }
}
