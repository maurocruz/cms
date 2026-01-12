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

	/**
	 * @param string $input
	 * @return string
	 */
	function camelCaseToSentence(string $input): string
	{
		return ToolBox::camelCaseToSentence($input);
	}

	/**
	 * @param $url
	 * @return string
	 */
	public function encodeUrlForSitemap($url): string
	{
		$parsed = parse_url($url);
		$scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
		$host   = $parsed['host'] ?? '';
		$port   = isset($parsed['port']) ? ':' . $parsed['port'] : '';
		$path   = isset($parsed['path']) ? implode('/', array_map('rawurlencode', explode('/', $parsed['path']))) : '';
		$query  = '';
		if (isset($parsed['query'])) {
			parse_str($parsed['query'], $queryParams);
			$query = '?' . http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
		}
		return $scheme . $host . $port . '/' . ltrim($path, '/') . $query;
	}
}
