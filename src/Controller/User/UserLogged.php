<?php
namespace Plinct\Cms\Controller\User;

use Plinct\Cms\CmsFactory;

class UserLogged
{
	private static ?string $token = null;
	private static ?int $iduser = null;
	private static ?string $name = null;
	private static ?array $privileges = null;

	/**
	 * @param string|null $token
	 */
	public function setToken(?string $token): void
	{
		self::$token = $token;
	}

	/**
	 * @return string|null
	 */
	public function getToken(): ?string
	{
		return self::$token;
	}

	/**
	 * @param int $iduser
	 */
	public function setIduser(int $iduser): void
	{
		self::$iduser = $iduser;
	}

	/**
	 * @return ?int
	 */
	public function getIduser(): ?int
	{
		return self::$iduser;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		self::$name = $name;
	}

	/**
	 * @return string|null
	 */
	public function getName(): ?string
	{
		return self::$name;
	}

	/**
	 * @param array $privileges
	 */
	public function setPrivileges(array $privileges): void
	{
		self::$privileges = $privileges;
	}

	/**
	 * @return ?array
	 */
	public function getPrivileges(): ?array
	{
		if (self::$iduser && !self::$privileges) {
			$data = CmsFactory::model()->type('user')->get(['iduser' => self::$iduser, 'properties'=>'privileges']);
			self::$privileges = $data[0]['privileges'];
		}

		return self::$privileges;
	}

	public function hasPrivileges(int $function, string $actions, string $namespace): bool
	{
		foreach ($this->getPrivileges() as $value)
		{
			$functionValue = $value['function'];
			$actionValue = $value['action'];
			$namespaceValue = $value['namespace'];

			if ($functionValue == 5 && $actionValue == 'crud' && $namespaceValue == 'all') return true;
			return (
				$functionValue > $function
				&&  $this->permittedActions($actions, $actionValue)
				&& ($namespaceValue === 'all' || $namespaceValue == $namespace)
			);
		}

		return false;
	}

	/**
	 * @param string $needled
	 * @param string $haystacked
	 * @return bool
	 */
	private function permittedActions(string $needled, string $haystacked): bool
	{
		$returns = false;
		if (str_contains($needled, 'c')) $returns = str_contains($haystacked, 'c');
		if (str_contains($needled, 'r')) $returns = str_contains($haystacked, 'r');
		if (str_contains($needled, 'u')) $returns = str_contains($haystacked, 'u');
		if (str_contains($needled, 'd')) $returns = str_contains($haystacked, 'd');
		return $returns;
	}
}
