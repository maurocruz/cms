<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Book;

use Plinct\Cms\CmsFactory;

class BookController
{
	/**
	 * @return null
	 */
	public function index()
	{
		return null;
	}

	/**
	 * @return null
	 */
	public function new()
	{
		return null;
	}

	/**
	 * @param array|null $params
	 * @return mixed|string|null
	 */
	public function edit(array $params = null)
	{
		$data = CmsFactory::request()->api()->get('book', array_merge($params,['properties'=>'image']))->ready();
		if (!empty($data)) {
			return $data[0];
		}
		return null;
	}
}