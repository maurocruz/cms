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
		$className = __NAMESPACE__.'\\'.ucfirst($typeName).'\\'.ucfirst($typeName);
		$classNameView = __NAMESPACE__.'\\'.ucfirst($typeName).'\\'.ucfirst($typeName).'View';
		$classNameCreativeWork = __NAMESPACE__.'\\CreativeWork\\'.ucfirst($typeName);
		$classNameCreativeWorkView = __NAMESPACE__.'\\CreativeWork\\'.ucfirst($typeName).'View';
		$classNameCreativeWorkItem = __NAMESPACE__.'\\CreativeWork\\'.ucfirst($typeName).'\\'.ucfirst($typeName).'View';
		$classNameIntagible = __NAMESPACE__.'\\Intangible\\'.ucfirst($typeName);
		$classNameIntagibleView = __NAMESPACE__.'\\Intangible\\'.ucfirst($typeName).'View';
		$classNameIntagibleIntoFolder = __NAMESPACE__.'\\Intangible\\'.ucfirst($typeName).'\\'.ucfirst($typeName).'View';
		if (class_exists($className)) {
			$this->object = new $className();
		} elseif(class_exists($classNameView)) {
			$this->object = new $classNameView();
		} elseif (class_exists($classNameCreativeWork)) {
			$this->object = new $classNameCreativeWork();
		} elseif(class_exists($classNameCreativeWorkView)) {
			$this->object = new $classNameCreativeWorkView();
		} elseif (class_exists($classNameCreativeWorkItem)) {
			$this->object = new $classNameCreativeWorkItem();
		} elseif (class_exists($classNameIntagible)) {
			$this->object = new $classNameIntagible();
		} elseif (class_exists($classNameIntagibleView)) {
			$this->object = new $classNameIntagibleView();
		} elseif (class_exists($classNameIntagibleIntoFolder)) {
			$this->object = new $classNameIntagibleIntoFolder();
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
		}
		return true;
	}
}
