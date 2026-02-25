<?php
namespace Plinct\Cms\Http\Controllers\Auth;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Authentication\LoginUser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class LoginAuthehticationController
{
	public function __construct(private LoginUser $loginUser)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		unset($params['submit']);
		$loginResponse = $this->loginUser->login($params['email'], $params['password']);
		// IF FAIL
		if (isset($loginResponse['status']) && $loginResponse['status'] === "fail")	{
			return $response->withHeader("Location", "/admin/auth/login?wrn=".urlencode($loginResponse['message']))->withStatus(302);
		} elseif ($loginResponse) {
			return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
		} else {
			return $response->withHeader("Location", "/admin/auth/login?wrn=".urlencode("Login failed"))->withStatus(302);
		}
	}

}
