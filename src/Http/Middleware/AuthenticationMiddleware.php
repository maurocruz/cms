<?php
namespace Plinct\Cms\Http\Middleware;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Auth\AuthenticatedUser;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Infrastructure\Http\ApiClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
	private ApiClient $api;
	private AuthenticatedUser $authUser;

	public function __construct(ApiClient $api, AuthenticatedUser $authUser)
	{
		$this->api = $api;
		$this->authUser = $authUser;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 * @return ResponseInterface
	 * @throws Exception
	 * @throws GuzzleException
	 */
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
		$authAttr = ['name' => null, 'uid' => null];
	  $token = $_COOKIE['API_TOKEN'] ?? null;
		$context = $request->getAttribute(RequestContext::class);

    if (session_status() === PHP_SESSION_NONE) {
			session_start();
    }

    if ($token) {
	    $tokenParts = explode(".", $token);
	    if (count($tokenParts) == 3) {
				// ARMAZENA O TOKEN NA API
				$this->api->setUserToken($token);
				// VALIDA O TOKEN
		    if ($this->api->get("auth/tokenValidator")) {
					// VERIFICA SE JÁ EXISTE UMA SESSÃO ABERTA
			    if (isset($_SESSION['userLogin'])) {
				    $name = $_SESSION['userLogin']['name'];
				    $uid = $_SESSION['userLogin']['uid'];
			    } else {
				    $payload = $tokenParts[1];
				    $json = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload));
				    $jsonArray = json_decode($json, true);
				    $name = $jsonArray['name'];
				    $uid = $jsonArray['uid'];
				    $_SESSION['userLogin']['name'] = $name;
				    $_SESSION['userLogin']['uid'] = $uid;
			    }

					$this->authUser->load($uid,$name,$token);
					// SET USER IN CONTEXT
					$context->setUser($this->authUser->getUser());

			    $authAttr['name'] = $name;
			    $authAttr['uid'] = $uid;
		    } else {
			    unset($_SESSION['userLogin']);
		    }
	    }
    }	else {
      unset($_SESSION['userLogin']);
    }
		session_write_close();
	  $request = $request->withAttribute("auth", $authAttr);
    return $handler->handle($request);
  }
}
