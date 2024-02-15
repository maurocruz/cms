<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Type;

use Plinct\Cms\CmsFactory;
use Psr\Http\Message\ServerRequestInterface;

class TypeController
{
	private ?string $type;
	private string $methodName;
	private ?string $id;
	private ?array $queryParams;

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
				$className = __NAMESPACE__ . "\\" . ucfirst($this->type) . "\\" . ucfirst($this->type);
				if (class_exists($className)) {
					$object = new $className();
					if (method_exists($object, $this->methodName)) {
						$returns = $object->{$this->methodName}($this->queryParams);
					}
				}
				if (!$returns) {
					$returns = CmsFactory::view()->webSite()->type($this->type)->setMethodName($this->methodName)->ready();
				}

			}
			return $returns;
		} else {
			return CmsFactory::view()->webSite()->index()->view();
		}
	}
}
