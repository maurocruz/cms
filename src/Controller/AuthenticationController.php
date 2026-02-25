<?php
namespace Plinct\Cms\Controller;

use Exception;
use Plinct\Cms\CmsFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationController
{
	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 * @throws Exception
	 */
	public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parseBody = $request->getParsedBody();
		$location = pathinfo($_SERVER['HTTP_REFERER'])['basename'] == "register" ? "/admin" : $_SERVER['HTTP_REFERER'];
		$authentication = CmsFactory::model()->auth()->login($parseBody['email'], $parseBody['password']);
		// AUTHORIZED
		if ($authentication && $authentication['status'] == "success") {
			$token = $authentication['data']['token'] ?? null;
			if ($token) {
				$tokenParts = explode(".", $token);
				if (count($tokenParts) == 3) {
					// A parte [1] é o Payload (os dados)
					$payload = $tokenParts[1];
					// Decodifica o Base64Url para JSON
					$json = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload));
					$jsonArray = json_decode($json, true);
					$name = $jsonArray['name'];
					$uid = $jsonArray['uid'];
					$exp = $jsonArray['exp'];
					// cookie
					setcookie('API_TOKEN', $token, $exp, '/');
					// session
					session_start();
					$_SESSION['userLogin']['name'] = $name;
					$_SESSION['userLogin']['uid'] = $uid;
					// RETURN
					return $response->withHeader("Location", $location)->withStatus(302);
				} else {
					CmsFactory::view()->addMain([
						CmsFactory::view()->fragment()->message()->warning("Invalid token"),
						CmsFactory::view()->fragment()->auth()->login($authentication)
					]);
				}
			}
		}
		// UNAUTHORIZED
		CmsFactory::view()->clearMain();
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->auth()->login($authentication));
		// RESPONSE
		return CmsFactory::view()->writeBody($response);
	}
}
