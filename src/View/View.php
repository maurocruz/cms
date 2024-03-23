<?php
declare(strict_types=1);
namespace Plinct\Cms\View;

use Plinct\Cms\Controller\App;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\User\User;
use Plinct\Cms\View\WebSite\WebSite;
use Plinct\Cms\View\WebSite\WebSiteFactory;
use Plinct\Tool\Logger\Logger;
use Psr\Http\Message\ResponseInterface;

class View
{
	/**
	 * @return null
	 */
	public function createWebSite() {
		return (new WebSiteFactory())->create();
	}

	/**
	 * @param $content
	 * @return null
	 */
	public function addMain($content) {
		return WebSiteFactory::addMain($content);
	}

	/**
	 * @param $content
	 * @param bool $firstChild
	 * @return null
	 */
	public function addHeader($content, bool $firstChild = false)
	{
		return WebSiteFactory::addHeader($content, $firstChild);
	}

	/**
	 * @param $bundle
	 * @return null
	 */
	public function addBundle($bundle)
	{
		return WebSiteFactory::addBundle($bundle);
	}

	/**
	 * @return Fragment
	 */
	public function fragment(): Fragment
	{
		return new Fragment();
	}

	public function clearMain()
	{
		WebSiteFactory::clearMain();
	}
	/**
	 * @param string $channel
	 * @param string $filename
	 * @return Logger
	 */
	public function Logger(string $channel, string $filename = 'logs.log'): Logger
	{
		return new Logger($channel, App::getLogdir().$filename);
	}

	/**
	 * @return User
	 */
	public function user(): User
	{
		return new User();
	}

	/**
	 * @return WebSite
	 */
	public function webSite(): WebSite
	{
		return new WebSite();
	}

	/**
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function writeBody(ResponseInterface $response): ResponseInterface
	{
		WebSiteFactory::buildBodyStructure();
		$response->getBody()->write(WebSiteFactory::ready());
		return $response;
	}
}
