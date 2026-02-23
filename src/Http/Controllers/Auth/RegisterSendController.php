<?php
namespace Plinct\Cms\Http\Controllers\Auth;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Authentication\RegisterUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class RegisterSendController
{

	public function __construct(private RegisterUseCase $register)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		unset($params['submit']);
		$location = $this->register->register($params);
		return $response->withHeader("Location", $location)->withStatus(302);
	}
}
