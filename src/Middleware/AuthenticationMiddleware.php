<?php
declare(strict_types=1);
namespace Plinct\Cms\Middleware;

use Firebase\JWT\JWT;
use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
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
    if (session_status() === PHP_SESSION_NONE) session_start();
    $token = $_COOKIE['API_TOKEN'] ?? null;
    if (App::getApiSecretKey()) {
      $tokenDecode = $token ? JWT::decode($token, App::getApiSecretKey(), ["HS256"]) : null;
      if ($token) {
        $_SESSION['userLogin']['name'] = $tokenDecode->name;
        $_SESSION['userLogin']['uid'] = $tokenDecode->uid;
        CmsFactory::request()->user()->userLogged()->setIduser($tokenDecode->uid);
        CmsFactory::request()->user()->userLogged()->setName($tokenDecode->name);
				CmsFactory::request()->user()->userLogged()->setToken($token);
      } else {
        unset($_SESSION['userLogin']);
      }
    } else {
      $request = $request->withAttribute("ApiSecretKey", false);
    }
		// VIEW FORM LOGIN
	  if (!CmsFactory::request()->user()->userLogged()->getIduser()) {
		  CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->auth()->login());
	  }
    return $handler->handle($request);
  }
}
