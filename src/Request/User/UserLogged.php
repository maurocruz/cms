<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\User;


class UserLogged
{
	private static ?string $token = null;
	private static ?string $iduser = null;
	private static string $name;
	private static array $privileges;

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
	 * @return string
	 */
	public function getName(): string
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
	 * @return array
	 */
	public function getPrivileges(): array
	{
		return self::$privileges;
	}
}
