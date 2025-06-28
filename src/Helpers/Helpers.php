<?php
namespace Plinct\Cms\Helpers;

use Plinct\Tool\ToolBox;
use Plinct\Tool\TypeBuilder;

class Helpers
{
	/**
	 * @param string $type
	 * @return Sitemap
	 */
	public function sitemap(string $type): Sitemap
	{
		return new Sitemap($type);
	}

	/**
	 * @param array $value
	 * @return TypeBuilder
	 */
	public function typeBuilder(array $value): TypeBuilder
	{
		return ToolBox::typeBuilder($value);
	}
}
