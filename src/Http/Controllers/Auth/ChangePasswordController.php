<?php
namespace Plinct\Cms\Http\Controllers\Auth;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Authentication\ChangePasswordUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ChangePasswordController
{
	public function __construct(private ChangePasswordUseCase $changePassword)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		$selector = $params['selector'] ?? null;
		$validator = $params['validator'] ?? null;
		$password = $params['password'] ?? null;
		$repeatPassword = $params['repeatPassword'] ?? null;
		if ($selector && $validator && $password && $repeatPassword) {
			$location = $this->changePassword->changePassword($selector, $validator, $password, $repeatPassword);
		} else {
			$location = "/admin/auth/reset-password?error=invalid-request";
		}
		return $response->withHeader('Location', $location)->withStatus(302);

	}
}
