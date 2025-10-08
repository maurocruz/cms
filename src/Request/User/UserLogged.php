<?php
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
			self::$privileges = $data[0]['privileges'] ?? null;
		}

		return self::$privileges;
	}

	public function hasPrivileges(int $function, string $actions, string $namespace): bool
	{
		if ($this->getPrivileges() === null) return false;
		foreach ($this->getPrivileges() as $value) {
			$functionValue = $value['function'];
			$actionsValue = $value['actions'];
			$namespaceValue = $value['namespace'];
			if ($functionValue == 5 && $actionsValue == 'crud' && $namespaceValue == 'all') {
				return true;
			}
			return $functionValue >= $function && str_contains($actionsValue, $actions) && (str_contains(strtolower($namespaceValue), strtolower($namespace)) || $namespaceValue == 'all');
		}
		return false;
	}
}
