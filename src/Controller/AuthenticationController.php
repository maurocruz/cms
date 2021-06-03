<?php
namespace Plinct\Cms\Controller;

use Firebase\JWT\JWT;
use Plinct\Cms\App;
use Plinct\Cms\Server\Api;

class AuthenticationController {

    public static function getTokenAccess($email, $password) {
        $auth = Api::login($email, $password);
        if ($auth['status'] == "Access authorized") {
            $token = $auth['data'];
            $tokenDecode = JWT::decode($token, App::getApiSecretKey(), ["HS256"]);
            if ($tokenDecode->admin) {
                setcookie('API_TOKEN', $token, $tokenDecode->exp);
                $_SESSION['userLogin'] = (array) $tokenDecode;
            }
            return $tokenDecode;
        }
        return false;
    }


}