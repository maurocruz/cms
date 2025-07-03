<?php
namespace Plinct\Cms\Controller\Type;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Helpers\Sitemap;

class ThingController implements TypeControllerInterface
{
	/**
	 * @var string
	 */
	protected string $type;
	/**
	 * @var string
	 */
	protected string $sitemapExtension = 'generic';

	/**
	 * @param string $type
	 */
	public function __construct(string $type = 'thing')
	{
		$this->type = $type;
	}

	/**
	 * @inheritDoc
	 */
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('thing')->ready();
	}

	/**
	 * @inheritDoc
	 */
	public function new(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('thing')->setMethodName('new')->setQueryParams($params)->ready();
	}

	/**
	 * @inheritDoc
	 */
	public function edit(array $params): bool
	{
		return true;
	}

	/**
	 * @throws Exception
	 */
	public function sitemap(array $params = []): bool
	{
		$data = CmsFactory::model()->type($this->type)->get(['orderBy'=>'dateModified','ordering'=>'desc','fields'=>'name,url,dateModified,dateCreated',"where"=>'`url` is not null']);
		if (isset($data['status']) && $data['status'] == 'fail') {
			$dataSitemap = [];
		} elseif ($this->sitemapExtension == 'news') {
			$dataSitemap = Sitemap::buildNewsSitemap($data);
		} else {
			$dataSitemap = Sitemap::buildGenericSitemap($data);
		}
		return CmsFactory::view()->webSite()->type($this->type)->setData($dataSitemap)->setQueryParams($params)->setMethodName('sitemap')->ready();
	}
}
