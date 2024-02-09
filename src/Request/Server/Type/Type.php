<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

class Type
{
	/**
	 * @var mixed|null
	 */
	private $type = null;

	private $params = null;

	public function __construct(string $type) {
		$classname = __NAMESPACE__.'\\'.ucfirst($type).'Server';
		if (class_exists($classname)) {
			$this->type = new $classname();
		}
		return $this->type;
	}

	/**
	 * @return bool
	 */
	public function typeExists(): bool {
		return !!$this->type;
	}

	/**
	 * @return mixed|null
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $method
	 * @return bool
	 */
	public function methodExists(string $method): bool {
		return method_exists($this->type, $method);
	}

	/**
	 * @param string $method
	 * @param array $params
	 * @return Type
	 */
	public function setParams(string $method, array $params): Type {
		$this->params = $this->typeExists() && $this->methodExists($method) ? $this->getType()->$method($params) : $params;
		return $this;
	}

	/**
	 * @return null
	 */
	public function getParams() {
		return $this->params;
	}
}
