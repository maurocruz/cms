<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\WebSite\Type;

use Plinct\Cms\Controller\Interfaces\TypeInterface;
use Plinct\Cms\Controller\WebSite\Type\Intangible\Intangible;

class Type implements TypeInterface
{
	private ?TypeInterface $type;

	public function __construct(string $type)
	{
		$typeClassName = __NAMESPACE__.'\\'.ucfirst($type).'\\'.ucfirst($type);

		if (class_exists($typeClassName)) {
			$this->type = new $typeClassName();
		}
	}

	public function getForm(string $tableHasPart, string $idHasPart, array $data = null) : array {
		return $this->type->getForm($tableHasPart, $idHasPart, $data);
	}


	/**
	 * @return Intangible
	 */
	public function intangible(): Intangible
	{
		return new Intangible();
	}


}