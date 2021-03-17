<?php
namespace Plinct\Cms\Server;

use Plinct\Tool\Curl;

class Api {
    private static $API_HOST;

    public static function setApiHost(string $absoluteUrl) {
        self::$API_HOST = $absoluteUrl;
    }

    public static function get(string $type, array $params = null): array {
        return json_decode((new Curl(self::$API_HOST))->get($type, $params), true);
    }
}