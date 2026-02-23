<?php
namespace Plinct\Cms\Application\Authentication;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Domain\Auth\User;
use Plinct\Cms\Infrastructure\Auth\ApiAuthProvider;

class AuthenticatedUser
{
	private User $user;
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

		$this->user = new User(
			$iduser,
			$name,
			$token,
			$privileges
		);
	}

	public function getUser(): ?User
	{
		return $this->user;
	}
}
