<?php
namespace Plinct\Cms\Helpers;

use Plinct\Tool\DateTime\DateTimeInterface;
use Plinct\Tool\ToolBox;
use Plinct\Tool\TypeBuilder;

class CmsHelper
{
	/**
	 * @param string $input
	 * @return string
	 */
	public static function camelCaseToSentence(string $input): string
	{
		return ToolBox::camelCaseToSentence($input);
	}

	/**
	 * @param string|null $datetime
	 * @return DateTimeInterface
	 */
	public static function dateTime(string $datetime = null): DateTimeInterface
	{
		return ToolBox::dateTime($datetime);
	}

	/**
	 * @param array $value
	 * @return TypeBuilder
	 */
	public static function typeBuilder(array $value): TypeBuilder
	{
		return ToolBox::typeBuilder($value);
	}
}
