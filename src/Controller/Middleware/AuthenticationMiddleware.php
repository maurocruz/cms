<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Middleware;

use Firebase\JWT\JWT;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
  /**
   * @param ServerRequestInterface $request
   * @param RequestHandlerInterface $handler
   * @return ResponseInterface
   */
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
		$authAttr = ['name' => null, 'uid' => null];
    if (session_status() === PHP_SESSION_NONE) {
			session_start();
    }
    $token = $_COOKIE['API_TOKEN'] ?? null;
    if (App::getApiSecretKey()) {
      $tokenDecode = $token ? JWT::decode($token, App::getApiSecretKey(), ["HS256"]) : null;
      if ($token) {
	      $name = $tokenDecode->name;
	      $uid = $tokenDecode->uid;
	      $_SESSION['userLogin']['name'] = $name ;
	      $_SESSION['userLogin']['uid'] = $uid;
				$authAttr['name'] = $name;
				$authAttr['uid'] = $uid;
        CmsFactory::controller()->user()->userLogged()->setName($name);
	      CmsFactory::controller()->user()->userLogged()->setIduser($uid);
				CmsFactory::controller()->user()->userLogged()->setToken($token);
      } else {
        unset($_SESSION['userLogin']);
      }
    } else {
      $request = $request->withAttribute("ApiSecretKey", false);
    }
		session_write_close();
	  $request = $request->withAttribute("auth", $authAttr);
    return $handler->handle($request);
  }
}
