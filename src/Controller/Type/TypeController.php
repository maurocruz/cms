<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Type;

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
			$this->queryParams['id'] = $this->id;
		}
	}

	/**
	 * @return true|null
	 */
	public function ready(): ?bool
	{
		$returns = null;
		if ($this->type) {
			// check if table sql exists
			$data = CmsFactory::model()->api()->get('config/database',['showTableStatus'=>lcfirst($this->type)])->ready();
			if ($data['message'] === "table not exists") {
				CmsFactory::view()->webSite()->configuration()->installSqlTable($this->type);
			} else {
				// if moduyle has controller class
				$className = __NAMESPACE__ . "\\" . ucfirst($this->type) . "\\" . ucfirst($this->type);
				if (class_exists($className)) {
					$object = new $className();
					if (method_exists($object, $this->methodName)) {
						$returns = $object->{$this->methodName}($this->queryParams);
					}
				}
				// if not module controller class
				if (!$returns) {
					// debug logger
					CmsFactory::view()->Logger('debug','debug.log')->debug("Controller Module not exist", ['type'=>$this->type,'method'=>__METHOD__]);
					// generic model
					$dataType = CmsFactory::model()->api()->get($this->type, $this->queryParams)->ready();
					$returns = CmsFactory::view()->webSite()->type($this->type)->setMethodName($this->methodName)->setData($dataType)->ready();
				}
			}
			return $returns;
		} else {
			return CmsFactory::view()->webSite()->index()->view();
		}
	}
}
