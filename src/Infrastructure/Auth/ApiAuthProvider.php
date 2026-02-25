<?php
namespace Plinct\Cms\Infrastructure\Auth;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Infrastructure\Http\ApiClient;

class ApiAuthProvider
{
	private ApiClient $client;

	public function __construct(ApiClient $client)
	{
		$this->client = $client;
	}

	/**
	 * @throws GuzzleException
	 */
	public function changePassword(string $selector, string $validator, string $password, string $repeatPassword): array
	{
		$response = $this->client->post('auth/change_password', ['selector'=>$selector,'validator'=>$validator,'password'=>$password,'repeatPassword'=>$repeatPassword]);
		return $this->returnResponse($response);
	}

	/**
	 * @throws GuzzleException
	 */
	public function getPrivileges(int $iduser, string $token): array
	{
		$this->client->setQueries(['iduser'=>$iduser,'properties'=>'privileges']);
		$this->client->setUserToken($token);
		$userData = $this->client->get('user');

		if (isset($userData[0])) {
			$value = $userData[0];
			return $value['privileges'] ?? [];
		}
		return [];
	}

	/**
	 * @throws GuzzleException
	 */
	public function login(string $email, string $password): array
	{
		$this->client->setQueries([]);
		$response = $this->client->post('auth/login', ['email'=>$email,'password'=>$password]);
		return $this->returnResponse($response);
	}

	/**
	 * @throws GuzzleException
	 */
	public function register(string $name, string $email, string $password, string $passwordRepeat): array
	{
		$response = $this->client->post('auth/register', ['name'=>$name,'email'=>$email,'password'=>$password, 'passwordRepeat'=>$passwordRepeat]);
		return $this->returnResponse($response);
	}

	/**
	 * @param string $email
	 * @param string $mailHost
	 * @param string $mailUsername
	 * @param string $mailPassword
	 * @param string $urlToResetPassword
	 * @return array
	 * @throws GuzzleException
	 */
	public function resetPassword(string $email, string $mailHost, string $mailUsername, string $mailPassword, string $urlToResetPassword): array
	{
		$responseApiData = $this->client->post('auth/reset_password', ['email' => $email, 'mailHost'=>$mailHost, 'mailUsername'=>$mailUsername, 'mailPassword'=>$mailPassword, 'urlToResetPassword'=>$urlToResetPassword]);
		return $this->returnResponse($responseApiData);
	}

	/**
	 * @param $response
	 * @return array
	 */
	private function returnResponse($response): array
	{
		if (isset($response['status'])){
			return $response;
		} elseif (isset($response['reasonPhrase'])) {
			return ['status'=>'fail','message'=>$response['reasonPhrase']." in ".$response['url']." method ".$response['method'] ,'data'=>$response];
		}
		return ['status'=>'fail','message'=>_('Un error occured while logging in'),'data'=>$response];
	}
}
