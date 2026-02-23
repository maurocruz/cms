<?php
namespace Plinct\Cms\Application\Authentication;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Infrastructure\Auth\JwtDecoderHttp;
use Plinct\Cms\Infrastructure\Auth\ApiAuthProvider;

readonly class LoginUser
{
	public function __construct(private ApiAuthProvider $apiAuthProvider, private JwtDecoderHttp $jwtDecoderHttp) {

	}

	/**
	 * @throws GuzzleException
	 */
	public function login(string $email, string $password): false|array
	{
		$responseLogin = $this->apiAuthProvider->login($email, $password);
		if (isset($responseLogin['status']) && $responseLogin['status'] === "fail") {
			return $responseLogin;
		} elseif (isset($responseLogin['status']) && $responseLogin['status'] === "success") {
			// LER O TOKEN E SALVAR EM USER E COOKIE
			return $this->jwtDecoderHttp->decode($responseLogin['data']['token']);
		}
		return false;
	}
}
