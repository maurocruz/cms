<?php
namespace Plinct\Cms\Controller\Type;

use Exception;
use Plinct\Cms\CmsFactory;
use Psr\Http\Message\ServerRequestInterface;

class TypeController
{
	/**
	 * @var string|mixed|null
	 */
	private ?string $type;
	/**
	 * @var string|mixed
	 */
	private string $methodName;
	/**
	 * @var string|mixed|null
	 */
	private ?string $id;
	/**
	 * @var array|null
	 */
	private ?array $queryParams;

	/**
	 * @param ServerRequestInterface $request
	 */
	public function __construct(ServerRequestInterface $request)
	{
		$this->type = $request->getAttributes()['type'] ?? null;
		$this->methodName = $request->getAttributes()['methodName'] ?? 'index';
		$this->id = $request->getAttributes()['id'] ?? null;
		$this->queryParams = $request->getQueryParams();
		if ($this->id) {
			$this->queryParams['id'.lcfirst($this->type)] = $this->id;
		}
	}

	/**
	 * @return true|null
	 * @throws Exception
	 */
	public function ready(): ?bool
	{
		$returns = false;
		$object = null;
		if ($this->type) {
			// check if table SQL exists
			$data = CmsFactory::model()->api()->get('config/database',['showTableStatus'=>lcfirst($this->type)])->ready();
			if (isset($data['message']) && $data['message'] === "table not exists" && in_array(strtolower($this->type), array_map('strtolower', CmsFactory::controller()->configuration()->getModulesEnabled()))) {
				CmsFactory::view()->webSite()->configuration()->installSqlTable($this->type);
			} else {
				// if moduyle has controller class
				$className = __NAMESPACE__ . "\\" . ucfirst($this->type) . "\\" . ucfirst($this->type).'Controller';
				$classNameCreativeWork = __NAMESPACE__ . "\\CreativeWork\\" . ucfirst($this->type).'Controller';
				$classNameIntangible = __NAMESPACE__ . "\\Intangible\\" . ucfirst($this->type).'Controller';
				if (class_exists($className)) {
					$object = new $className();
				} elseif(class_exists($classNameIntangible)) {
					$object = new $classNameIntangible();
				} elseif(class_exists($classNameCreativeWork)) {
					$object = new $classNameCreativeWork();
				}
				if ($object && method_exists($object, $this->methodName)) {
					$returns = $object->{$this->methodName}($this->queryParams);
				}
				// if not module controller class
				if ($returns === false) { // generic model
					$dataType = CmsFactory::model()->api()->get($this->type, $this->queryParams)->ready();
					$returns = CmsFactory::view()->webSite()->type($this->type)->setMethodName($this->methodName)->setData($dataType)->ready();
				}
			}
			return $returns;
		}
		// INDEX
		else {
			CmsFactory::view()->webSite()->index()->view();
			return true;
		}
	}
}
