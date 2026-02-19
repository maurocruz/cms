<?php
namespace Plinct\Cms\Application\Auth;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Domain\Auth\User;
use Plinct\Cms\Infrastructure\Auth\ApiPrivilegeProvider;

class AuthenticatedUser
{
	private User $user;
	private ApiPrivilegeProvider $privilegeProvider;

	public function __construct(ApiPrivilegeProvider $privilegeProvider)
	{
		$this->privilegeProvider = $privilegeProvider;
	}

	/**
	 * @throws GuzzleException
	 */
	public function load(int $iduser, string $name, string $token): void
	{
		$privileges = $this->privilegeProvider->getPrivileges($iduser, $token);

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
