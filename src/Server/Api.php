<?php
namespace Plinct\Cms\Server;

use Plinct\Tool\Curl;

class Api {
    private static $API_HOST;

    public static function setApiHost(string $absoluteUrl) {
        self::$API_HOST = $absoluteUrl;
    }

    public static function get(string $type, array $params = null): array {
        if (self::$API_HOST) {
            $relativeUrl = lcfirst($type);
            return json_decode((new Curl(self::$API_HOST))->get($relativeUrl, $params), true);
        } else {
            $className = "Plinct\\Api\\Type\\".ucfirst($type);
            if (class_exists($className)) {
                return (new $className())->get($params);
            }
            return [];
        }
    }
}