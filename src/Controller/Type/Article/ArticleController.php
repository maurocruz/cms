<?php
namespace Plinct\Cms\Controller\Type\Article;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\CreativeWork\CreativeWorkController;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class ArticleController extends CreativeWorkController implements TypeControllerInterface
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->sitemapExtension = 'news';
		parent::__construct('article');
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('article')->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function new(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('article')->setMethodName('new')->setQueryParams($params)->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 * @throws Exception
	 */
  public function edit(array $params): bool
  {
		$data = CmsFactory::model()->type('article')->get($params);
		return CmsFactory::view()->webSite()->type('article')->setData($data)->setMethodName('edit')->ready();
  }
}
