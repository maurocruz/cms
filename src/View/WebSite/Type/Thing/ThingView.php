<?php
namespace Plinct\Cms\View\WebSite\Type\Thing;

use DOMException;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class ThingView extends ThingElements implements TypeViewInterface
{
	/**
	 * @var string
	 */
	protected string $type;
	/**
	 * @var string
	 */
	protected string $sitemapFilename;
	/**
	 * @var string
	 */
	protected string $sitemapExtension = 'generic';

	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'thing', string $sitemapFilename = 'sitemap.xml')
	{
		$this->type = $type;
		$this->sitemapFilename = $sitemapFilename;
	}

	/**
	 *
	 */
	public function __destruct() {
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('thing')
				->title(_("Things"))
				->newTab("/admin/thing", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/thing/new", CmsFactory::view()->fragment()->icon()->plus())
				->newTab("/admin/thing/sitemap", CmsFactory::view()->fragment()->icon()->sitemap())
				->search()
				->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('thing')->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function new(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(self::form(),_('New thing'))
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
	}

	/**
	 * @param array $data
	 * @param array|null $queryParams
	 * @return void
	 * @throws DOMException
	 */
	public function sitemap(array $data, array $queryParams = null): void
	{
		$sitemap = CmsFactory::helpers()->sitemap($this->type);
		$sitemap->setFilename($this->sitemapFilename);
		$sitemap->setNamespace($this->sitemapExtension);
		$sitemap->setDataSitemap($data);
		$result = $sitemap->saveSitemap();
		CmsFactory::view()->fragment()->sitemapReturn($result, $sitemap->getFilename());
	}
}
