<?php
namespace Plinct\Cms\Application\Authentication;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Domain\Auth\Userlogged;
use Plinct\Cms\Infrastructure\Auth\ApiAuthProvider;

class AuthenticatedUser
{
	private Userlogged $user;
	private ApiAuthProvider $apiAuthProvider;

	public function __construct(ApiAuthProvider $apiAuthProvider)
	{
		$this->apiAuthProvider = $apiAuthProvider;
	}

	/**
	 * @throws GuzzleException
	 */
	public function load(int $iduser, string $name, string $token): void
	{
		$privileges = $this->apiAuthProvider->getPrivileges($iduser, $token);

		$this->user = new Userlogged(
			$iduser,
			$name,
			$token,
			$privileges
		);
	}

	public function getUser(): ?Userlogged
	{
		return $this->user;
	}
}
