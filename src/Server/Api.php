<?php
namespace Plinct\Cms\Server;

use Plinct\Cms\App;
use Plinct\Tool\Curl;

class Api {

    public static function get(string $type, array $params = null): array {
        $relativeUrl = lcfirst($type);
        return json_decode((new Curl(App::getApiHost()))->get($relativeUrl, $params), true);
    }

    public static function login(string $email, string $password) {
        return json_decode((new Curl(App::getApiHost()))->post("login", [ "email" => $email, "password" => $password ] ));
    }

    public static function register() {

    }
}