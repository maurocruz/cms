<?php
namespace Plinct\Cms\Controller\Type;

class Type
{
	/**
	 * @var string
	 */
	private string $type;
	/**
	 * @var array|null
	 */
	private ?array $params = null;
	/**
	 * @var string
	 */
	private string $method = 'index';

	/**
	 * @param string $type
	 */
	public function __construct(string $type)
	{
		$this->type = $type;
	}

	/**
	 * @param string $method
	 * @return Type
	 */
	public function setMethod(string $method): Type
	{
		$this->method = $method;
		return $this;
	}

	/**
	 * @param array|null $params
	 * @return Type
	 */
	public function setParams(?array $params): Type
	{
		$this->params = $params;
		return $this;
	}

	/**
	 * @return false|mixed
	 */
	public function ready(): mixed
	{
		$className = __NAMESPACE__ . '\\' . ucfirst($this->type) . '\\' . ucfirst($this->type).'Controller';
		if (class_exists($className)) {
			$controller = new $className();
			if(method_exists($controller, $this->method)) {
				return $controller->{$this->method}($this->params);
			}
		}
		return false;
	}
}
