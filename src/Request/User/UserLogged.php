<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\User;

use Plinct\Cms\CmsFactory;

class UserLogged
{
	private static ?string $token = null;
	private static ?string $iduser = null;
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
	 * @param string $iduser
	 */
	public function setIduser(string $iduser): void
	{
		self::$iduser = $iduser;
	}

	/**
	 * @return ?string
	 */
	public function getIduser(): ?string
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
			$data = CmsFactory::request()->user()->get(['iduser' => self::$iduser, 'properties'=>'privileges']);
			self::$privileges = $data[0]['privileges'];
		}

		return self::$privileges;
	}

	public function hasPrivileges(int $function, string $actions, string $namespace): bool
	{
		foreach ($this->getPrivileges() as $value)
		{
			$functionValue = $value['function'];
			$actionsValue = $value['actions'];
			$namespaceValue = $value['namespace'];

			if ($functionValue == 5 && $actionsValue == 'crud' && $namespaceValue == 'all') return true;
			return (
				$functionValue > $function
				&& $this->permittedActions($actions, $actionsValue)
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
		if (strpos($needled,'c') !== false) $returns = strpos($haystacked,'c') !== false;
		if (strpos($needled,'r') !== false) $returns = strpos($haystacked,'r') !== false;
		if (strpos($needled,'u') !== false) $returns = strpos($haystacked,'u') !== false;
		if (strpos($needled,'d') !== false) $returns = strpos($haystacked,'d') !== false;
		return $returns;
	}
}
