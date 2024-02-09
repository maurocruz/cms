<?php
declare(strict_types=1);
namespace Plinct\Cms\WebSite\Type\Book;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

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
		$data = CmsFactory::request()->api()->get('book', $params)->ready();
		if (!empty($data)) {
			return $data[0];
		}
		return null;
	}
	/**
	 */
	public function saveSitemap()
	{
		$dataSitemap = null;
		$data = CmsFactory::request()->api()->get("book",	['orderBy'=>'name', 'limit'=>'none'])->ready();
		foreach ($data as $value) {
			if ($value['datePublished']) {
				$dataSitemap[] = [
					"loc" => App::getURL().'/catalogo/book/'.$value['idbook'],
					'lastmod' => DateTime::formatISO8601($value['dateModified'])
				];
			}
		}
		(new Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-book.xml"))->saveSitemap($dataSitemap);
	}
}
