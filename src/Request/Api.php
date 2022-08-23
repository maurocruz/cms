<?php

declare(strict_types=1);

namespace Plinct\Cms\Request;

use Plinct\Cms\CmsFactory;

class Api
{
	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return mixed
	 */
	public static function get(string $relativeUrl, array $params = []) {
		return CmsFactory::request()->api()->get($relativeUrl, $params)->ready();
	}

	/**
	 * @param string $relativeUrl
	 * @param array $data
	 * @return mixed
	 */
	public static function post(string $relativeUrl, array $data) {
		return CmsFactory::request()->api()->post($relativeUrl, $data)->ready();
	}

	/**
	 * @param string $relativeUrl
	 * @param array $data
	 * @return mixed
	 */
	public static function put(string $relativeUrl, array $data) {
		return CmsFactory::request()->api()->put($relativeUrl, $data)->ready();
	}
	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return mixed
	 */
	public static function delete(string $relativeUrl, array $params) {
		return CmsFactory::request()->api()->delete($relativeUrl, $params)->ready();
	}
}
