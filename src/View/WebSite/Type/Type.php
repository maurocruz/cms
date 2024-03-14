<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type;

use Plinct\Cms\CmsFactory;

class Type
{
	/**
	 * @var TypeInterface|mixed
	 */
	private ?TypeInterface $object = null;
	/**
	 * @var string|null
	 */
	private ?string $methodName = 'index';
	/**
	 * @var array|null
	 */
	private ?array $data = [];

	/**
	 * @param string $typeName
	 */
	public function __construct(string $typeName) {
		$className = __NAMESPACE__.'\\'.ucfirst($typeName).'\\'.ucfirst($typeName);
		if (class_exists($className)) {
			$this->object = new $className();
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
	 * @return bool
	 */
	public function ready(): bool
	{
		if (isset($this->object)) {
			switch ($this->methodName) {
				case 'edit': $this->object->edit($this->data); break;
				case 'new': $this->object->new($this->data); break;
				default:
					method_exists($this->object, $this->methodName)
						? $this->object->{$this->methodName}($this->data)
						: CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_('Method does not exist')));
			}
		} else {
			return false;
		}
		return true;
	}
}
