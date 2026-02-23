<?php
namespace Plinct\Cms\Http\Controllers\Auth;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Authentication\ResetPasswordUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ResetPasswordSendEmailController
{
	public function __construct(private ResetPasswordUseCase $resetPassword)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		unset($params['submit']);
		$responseApplication = $this->resetPassword->sendEmail($params['email']);
		if ($responseApplication) {
			$message = $responseApplication;
		} else {
			$message = "An error occurred! Please notify an administrator or try again later.";
		}
		$warncript = base64_encode(gzdeflate($message));
		$encodeWarning = urlencode($warncript);
		return $response->withHeader("Location", "/admin/auth/login?wrnc=$encodeWarning")->withStatus(302);
	}
}
