<?php
namespace Plinct\Cms\Model\Api;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Cms\Controller\App;

class WithConnectBd
{
	/**
	 * @var string
	 */
	private string $relativeUrl;
	/**
	 * @var array
	 */
	private array $params = [];
	/**
	 * @var string
	 */
	private string $method;

	/**
	 * @param string|null $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function get(string $relativeUrl = null, array $params = []): static
	{
		$this->method = 'GET';
		$this->relativeUrl = $relativeUrl;
		$this->params = $params;
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function post(string $relativeUrl, array $params): static
	{
		$this->method = 'POST';
		$this->relativeUrl = $relativeUrl;
		$this->params = $params;
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function put(string $relativeUrl, array $params): static
	{
		$this->method = 'PUT';
		$this->relativeUrl = $relativeUrl;
		$this->params = $params;
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function delete(string $relativeUrl, array $params): static
	{
		$this->method = 'DELETE';
		$this->relativeUrl = $relativeUrl;
		$this->params = $params;
		return $this;
	}

	/**
	 * @throws Exception
	 */
	public function ready(): array
	{
		// CONFIG
		if (str_contains($this->relativeUrl, 'config')) {
			if ($this->method == 'GET') {
				return ApiFactory::request()->configuration()->index(App::getDBNAME());
			} elseif ($this->method == 'POST') {
				$module = $this->params['module'] ?? null;
				if ($module) {
					return ApiFactory::request()->configuration()->module()->installModule($module);
				} else {
					return ['status'=>'fail','message'=>'Module was not created! Name is null!'];
				}
			}
		} elseif ($this->relativeUrl == 'auth/login') {
			return ApiFactory::request()->user()->authentication()->login($this->params);
		} elseif ($this->relativeUrl == 'auth/register' && $this->method == 'POST') {
			return ApiFactory::request()->user()->authentication()->register($this->params);
		} elseif ($this->relativeUrl == 'auth/reset_password' && $this->method == 'POST') {
			return ApiFactory::request()->user()->authentication()->resetPassword($this->params);
		} elseif ($this->relativeUrl == 'change_password' && $this->method == 'POST') {
			return ApiFactory::request()->user()->authentication()->changePassword($this->params);
		} elseif ($this->relativeUrl == 'user/privileges') {
			if ($this->method == 'GET') {
				return ApiFactory::request()->user()->privileges()->httpRequest()->withPrivileges('r','user_admin')->get($this->params);
			} elseif ($this->method == 'POST') {
				return ApiFactory::request()->user()->privileges()->httpRequest()->withPrivileges('c','user_admin')->post($this->params);
			} elseif ($this->method == 'PUT') {
				return ApiFactory::request()->user()->privileges()->httpRequest()->withPrivileges('u','user_admin')->put($this->params);
			} elseif ($this->method == 'DELETE') {
				return ApiFactory::request()->user()->privileges()->httpRequest()->withPrivileges('d','user_admin',3)->delete($this->params);
			}
		} elseif ($this->relativeUrl == 'user') {
			if ($this->method == 'GET') {
				return ApiFactory::request()->user()->get($this->params);
			} elseif ($this->method == 'POST') {
				return ApiFactory::request()->user()->httpRequest()->withPrivileges('c','user_admin')->post($this->params);
			} elseif ($this->method == 'PUT') {
				return ApiFactory::request()->user()->httpRequest()->withPrivileges('u','user_admin')->put($this->params);
			} elseif ($this->method == 'DELETE') {
				return ApiFactory::request()->user()->httpRequest()->withPrivileges('d','user_admin')->delete($this->params);
			}
		}
		$data = ApiFactory::request()->type($this->relativeUrl)->{$this->method}($this->params)->ready();
		return ApiFactory::response()->type($this->relativeUrl)->setData($data)->setParams($this->params)->ready();
	}
}
