<?php
namespace Plinct\Cms\Domain\Auth;

class User
{
	private int $iduser;
	private string $username;
	private string $token;
	private array $privileges;

	/**
	 * @param int $iduser
	 * @param string $username
	 * @param string $token
	 * @param array $privileges
	 */
	public function __construct(int $iduser, string $username, string $token, array $privileges = []) {
		$this->iduser = $iduser;
		$this->username = $username;
		$this->token = $token;
		$this->privileges = $privileges;
	}

	/**
	 * @return int
	 */
	public function getIduser(): int
	{
		return $this->iduser;
	}

	/**
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}

	/**
	 * @return string
	 */
	public function getToken(): string
	{
		return $this->token;
	}

	/**
	 * @param int $function
	 * @param string $actions
	 * @param string $namespace
	 * @return bool
	 */
	public function hasPrivilege(int $function, string $actions, string $namespace): bool
	{
		if ($this->getPrivileges() === null) return false;
		foreach ($this->getPrivileges() as $value) {
			$functionValue = $value['function'];
			$actionsValue = $value['action'];
			$namespaceValue = $value['namespace'];
			if ($functionValue == 5 && $actionsValue == 'crud' && $namespaceValue == 'all') {
				return true;
			}
			return $functionValue >= $function && str_contains($actionsValue, $actions) && (str_contains(strtolower($namespaceValue), strtolower($namespace)) || $namespaceValue == 'all');
		}
		return false;
	}

	/**
	 * @return array
	 */
	public function getPrivileges(): array
	{
		return $this->privileges;
	}
}
