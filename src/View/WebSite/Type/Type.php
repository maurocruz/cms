<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\WebSite\Type\Intangible\Intangible;

class Type
{
	private TypeInterface $object;
	private ?string $methodName = null;
	private array $data = [];

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
	 * @param array $data
	 * @return Type
	 */
	public function setData(array $data): Type
	{
		$this->data = $data;
		return $this;
	}

	public function getForm(string $tableHasPart, string $idHasPart, array $data = null) : array {
		return $this->object->getForm($tableHasPart, $idHasPart, $data);
	}

	public function ready()
	{
		if (isset($this->object)) {
			switch ($this->methodName) {
				case 'edit': CmsFactory::view()->addMain($this->object->edit($this->data)); break;
				case 'new': CmsFactory::view()->addMain($this->object->new($this->data)); break;
				default: CmsFactory::view()->addMain($this->object->index($this->data));
			}
		}
		return true;
	}

	/**
	 * @return Intangible
	 */
	public function intangible(): Intangible
	{
		return new Intangible();
	}
}