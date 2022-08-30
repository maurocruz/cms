<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\ImageObject;

use Exception;
use Plinct\Cms\Interfaces\TypeInterface;

class ImageObject extends ImageObjectView implements TypeInterface
{
	/**
	 * @throws Exception
	 */
	public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array {
		return parent::getForm($tableHasPart, $idHasPart, $data);
	}
}