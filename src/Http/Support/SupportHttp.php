<?php
namespace Plinct\Cms\Http\Support;

use Plinct\Tool\ToolBox;
use Plinct\Tool\TypeBuilder;

class SupportHttp
{
	/**
	 * @param string $string
	 * @return false|string
	 */
	public static function decodeCript(string $string): false|string
	{
		return gzinflate(base64_decode($string));
	}

	/**
	 * @param string $string
	 * @return false|string
	 */
	public static function encodeCript(string $string): false|string
	{
		return base64_encode(gzdeflate($string));
	}

	/**
	 * @param array $value
	 * @return TypeBuilder
	 */
	public static function typeBuilder(array $value): TypeBuilder
	{
		return ToolBox::typeBuilder($value);
	}

	/**
	 * @param string $input
	 * @return string
	 */
	public static function camelCaseToSentence(string $input): string
	{
		return ToolBox::camelCaseToSentence($input);
	}
}
