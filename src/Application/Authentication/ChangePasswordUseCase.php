<?php
namespace Plinct\Cms\Application\Authentication;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Abstracts\UseCaseAbstract;
use Plinct\Cms\Infrastructure\Auth\ApiAuthProvider;

class ChangePasswordUseCase extends UseCaseAbstract
{
	public function __construct(private readonly ApiAuthProvider $apiAuthProvider)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function changePassword(string $selector, string $validator, string $password, string $repeatPassword): string
	{
		$apiData = $this->apiAuthProvider->changePassword($selector, $validator, $password, $repeatPassword);
		if ($this->isSuccess($apiData)) {
			return "/admin/auth/login?wrn=".urlencode($apiData['message'] ?? '?');
		} else {
			return "/admin/auth/change-password?wrn=".urlencode($apiData['message'] ?? '?')."&selector=$selector&validator=$validator";
		}
	}
}
