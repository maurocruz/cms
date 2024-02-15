<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\ReactShell;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;

class ReactShell
{
	/**
	 * @var array
	 */
	private array $attributes = ['class'=>'plinct-shell'];

	/**
	 * @param string $type
	 * @param array $attributes
	 */
	public function __construct(string $type, array $attributes = [])	{
		$this->attributes['data-type'] = $type;
		$this->attributes = array_merge($this->attributes, $attributes);
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return $this
	 */
	public function setAttribute(string $name, string $value ): ReactShell
	{
		$this->attributes[$name] = $value;
		return $this;
	}

	/**
	 * @param string $tableName
	 * @return $this
	 */
	public function setTableHasPart(string $tableName): ReactShell
	{
		$this->setAttribute('data-tablehaspart', $tableName);
		return $this;
	}

	/**
	 * @param int $idHasPart
	 * @return $this
	 */
	public function setIdHasPart(int $idHasPart): ReactShell
	{
		$this->setAttribute('data-idhaspart',(string) $idHasPart);
		return $this;
	}

	/**
	 * @return string[]
	 */
	public final function ready(): array {
		$this->setAttribute('data-apihost', App::getApiHost());
		$this->attributes['data-usertoken'] = CmsFactory::controller()->user()->userLogged()->getToken();
		return ['tag'=>'div', 'attributes'=>$this->attributes];
	}
}