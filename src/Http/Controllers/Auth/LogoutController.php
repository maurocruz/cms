<?php
namespace Plinct\Cms\Http\Controllers\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LogoutController
{
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		unset($_SESSION['userLogin']);
		setcookie("API_TOKEN", "", time() - 3600,'/');
		return $response->withHeader("Location", "/admin")->withStatus(302);
	}

}
