<?php
namespace Plinct\Cms\View\WebSite\Type;

use Plinct\Cms\CmsFactory;

class Type
{
	/**
	 * @var TypeViewInterface|mixed
	 */
	private ?TypeViewInterface $object = null;
	/**
	 * @var string|null
	 */
	private ?string $methodName = 'index';
	/**
	 * @var array
	 */
	private array $queryParams = [];
	/**
	 * @var array|null
	 */
	private ?array $data = [];

	/**
	 * @param string $typeName
	 */
	public function __construct(string $typeName)
	{
		$classes = [
			__NAMESPACE__ . '\\' . ucfirst($typeName) . '\\' . ucfirst($typeName),
			__NAMESPACE__ . '\\' . ucfirst($typeName) . '\\' . ucfirst($typeName) . 'View',
			__NAMESPACE__ . '\\CreativeWork\\' . ucfirst($typeName),
			__NAMESPACE__ . '\\CreativeWork\\' . ucfirst($typeName) . 'View',
			__NAMESPACE__ . '\\CreativeWork\\' . ucfirst($typeName) . '\\' . ucfirst($typeName) . 'View',
			__NAMESPACE__ . '\\Intangible\\' . ucfirst($typeName),
			__NAMESPACE__ . '\\Intangible\\' . ucfirst($typeName) . 'View',
			__NAMESPACE__ . '\\Intangible\\' . ucfirst($typeName) . '\\' . ucfirst($typeName) . 'View'
		];
		foreach ($classes as $class) {
			if (class_exists($class)) {
				$this->object = new $class();
				break;
			}
		}
	}

	/**
	 * @param string|null $methodName
	 * @return Type
	 */
	public function setMethodName(?string $methodName): Type
	{
		$this->methodName = $methodName;
		return $this;
	}

	/**
	 * @param ?array $data
	 * @return Type
	 */
	public function setData(?array $data): Type
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * @param array $queryParams
	 * @return Type
	 */
	public function setQueryParams(array $queryParams): Type
	{
		$this->queryParams = $queryParams;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function ready(): bool
	{
		if (isset($this->data['status']) && $this->data['status'] === "fail") {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning($this->data['message']));
			return false;
		}
		if (isset($this->object)) {
			switch ($this->methodName) {
				case 'edit': $this->object->edit($this->data, $this->queryParams); break;
				case 'new': $this->object->new($this->data, $this->queryParams); break;
				default:
					method_exists($this->object, $this->methodName)
						? $this->object->{$this->methodName}($this->data, $this->queryParams)
						: CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_(sprintf("Method '%s' on '%s' does not exist", $this->methodName, get_class($this->object)))));
			}
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_("Object does not exist")));
			return false;
		}
		return true;
	}
}
