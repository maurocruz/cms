<?php
namespace Plinct\Cms\Application\Authentication;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Abstracts\UseCaseAbstract;
use Plinct\Cms\Infrastructure\Auth\ApiAuthProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ResetPasswordUseCase extends UseCaseAbstract
{
	private string $mailHost;
	private string $mailUsername;
	private string $mailPassword;
	private string $urlToResetPassword;

	public function __construct(ContainerInterface $container, private readonly ApiAuthProvider $apiAuthProvider)
	{
		$settings = [];
		try {
			$settings = $container->get('settings');
		} catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
			error_log($e->getMessage());
		}
		$this->mailHost = $settings['mailHost'] ?? '';
		$this->mailUsername = $settings['mailUsername'] ?? '';
		$this->mailPassword = $settings['mailPassword'] ?? '';
		$this->urlToResetPassword = $settings['urlToResetPassword'] ?? '';
	}

	/**
	 * @throws GuzzleException
	 */
	public function sendEmail(string $email)
	{
		$responseApiData = null;
		if ($this->mailHost && $this->mailUsername && $this->mailPassword && $this->urlToResetPassword) {
			$responseApiData = $this->apiAuthProvider->resetPassword($email, $this->mailHost, $this->mailUsername, $this->mailPassword, $this->urlToResetPassword);
		}
		if ($this->isSuccess($responseApiData)) {
			return $responseApiData['data']['mail']['message'];
		} else {
			return false;
		}
	}
}
