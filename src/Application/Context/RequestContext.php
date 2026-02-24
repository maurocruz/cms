<?php
namespace Plinct\Cms\Application\Context;

use Plinct\Cms\Application\Contracts\RequestContextInterface;
use Plinct\Cms\Domain\Auth\Userlogged;

class RequestContext implements RequestContextInterface
{
	private ?Userlogged $user = null;
	private array $modulesAvailable = [];
	private array $modulesEnabled = [];
	private string $theme = 'default';
	private string $locale = 'pt-br';
	private string $host = '';
	private string $apiHost = '';
	private string $sitename = '';
	private string $version = '';
	private string $basedirectory = '';
	private string $commit = '';
	private string $query = '';

	/**
	 * @param string $query
	 */
	public function setQuery(string $query): void
	{
		$this->query = $query;
	}

	/**
	 * @return string
	 */
	public function getQuery(): string
	{
		return $this->query;
	}

	/**
	 * @param string $commit
	 */
	public function setCommit(string $commit): void
	{
		$this->commit = $commit;
	}

	/**
	 * @return string
	 */
	public function getCommit(): string
	{
		return $this->commit;
	}

	/**
	 * @param string $basedirectory
	 */
	public function setBasedirectory(string $basedirectory): void
	{
		$this->basedirectory = $basedirectory;
	}

	/**
	 * @return string
	 */
	public function getBasedirectory(): string
	{
		return $this->basedirectory;
	}

	/**
	 * @param string $sitename
	 */
	public function setSitename(string $sitename): void
	{
		$this->sitename = $sitename;
	}

	/**
	 * @return string
	 */
	public function getSitename(): string
	{
		return $this->sitename;
	}

	/**
	 * @param string $version
	 */
	public function setVersion(string $version): void
	{
		$this->version = $version;
	}

	/**
	 * @return string
	 */
	public function getVersion(): string
	{
		return $this->version;
	}

	/**
	 * @param string $apiHost
	 */
	public function setApiHost(string $apiHost): void
	{
		$this->apiHost = $apiHost;
	}

	/**
	 * @return string
	 */
	public function getApiHost(): string
	{
		return $this->apiHost;
	}

	/**
	 * @param Userlogged $user
	 * @return void
	 */
	public function setUser(Userlogged $user): void
	{
		$this->user = $user;
	}

	/**
	 * @return Userlogged|null
	 */
	public function getUser(): ?Userlogged
	{
		return $this->user;
	}

	/**
	 * @param array $available
	 * @param array $enabled
	 * @return void
	 */
	public function setModules(array $available, array $enabled): void
	{
		$this->modulesAvailable = $available;
		$this->modulesEnabled = $enabled;
	}

	/**
	 * @param array $modulesAvailable
	 */
	public function setModulesAvailable(array $modulesAvailable): void
	{
		$this->modulesAvailable = $modulesAvailable;
	}

	/**
	 * @return array
	 */
	public function getModulesAvailable(): array
	{
		return $this->modulesAvailable;
	}

	/**
	 * @param array $modulesEnabled
	 */
	public function setModulesEnabled(array $modulesEnabled): void
	{
		$this->modulesEnabled = $modulesEnabled;
	}

	/**
	 * @return array
	 */
	public function getModulesEnabled(): array
	{
		return $this->modulesEnabled;
	}

	/**
	 * @param string $theme
	 * @return void
	 */
	public function setTheme(string $theme): void
	{
		$this->theme = $theme;
	}

	/**
	 * @return string
	 */
	public function getTheme(): string
	{
		return $this->theme;
	}

	/**
	 * @param string $locale
	 * @return void
	 */
	public function setLocale(string $locale): void
	{
		$this->locale = $locale;
	}

	/**
	 * @return string
	 */
	public function getLocale(): string
	{
		return $this->locale;
	}

	/**
	 * @param string $host
	 */
	public function setHost(string $host): void
	{
		$this->host = $host;
	}

	/**
	 * @return string
	 */
	public function getHost(): string
	{
		return $this->host;
	}
}

