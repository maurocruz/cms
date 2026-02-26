<?php
namespace Plinct\Cms\Support;

use Plinct\Tool\ToolBox;
use Plinct\Tool\TypeBuilder;

class Support
{
	public static function typeBuilder(array $value): TypeBuilder
	{
		return ToolBox::typeBuilder($value);
	}
}
