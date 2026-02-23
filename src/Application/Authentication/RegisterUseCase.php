<?php
namespace Plinct\Cms\Application\Authentication;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Abstracts\UseCaseAbstract;
use Plinct\Cms\Infrastructure\Auth\ApiAuthProvider;

class RegisterUseCase extends UseCaseAbstract
{
	 public function __construct(private readonly ApiAuthProvider $apiAuthProvider)
	 {
	 }

	/**
	 * @throws GuzzleException
	 */
	public function register(array $params): string
	 {
		 $name = $params['name'] ?? null;
		 $email = $params['email'] ?? null;
		 $password = $params['password'] ?? null;
		 $passwordRepeat = $params['passwordRepeat'] ?? null;
		 if ($name && $email && $password && $passwordRepeat) {
			 $responseData = $this->apiAuthProvider->register($name, $email, $password, $passwordRepeat);
			 if ($this->isSuccess($responseData)) {
				 return "/admin/auth/login?wrn=".urlencode($responseData['message'] ?? '?');
			 } else {
				 return "/admin/auth/register?wrn=".urlencode($responseData['message'] ?? '?');
			 }
		 } else {
			 return "/admin/auth/register?wrn=".urldecode('Please fill all the required fields');
		 }
	 }
}
