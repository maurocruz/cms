<?php
namespace Plinct\Cms\View;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Enclave\Enclave;
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
	public function createWebSite(): null
	{
		return (new WebSiteFactory())->create();
	}

	/**
	 * @param $content
	 * @return bool|null
	 */
	public function addMain($content): ?bool
	{
		return WebSiteFactory::addMain($content);
	}

	/**
	 * @param $content
	 * @param bool $firstChild
	 * @return null
	 */
	public function addHeader($content, bool $firstChild = false): null
	{
		return WebSiteFactory::addHeader($content, $firstChild);
	}

	/**
	 * @param $bundle
	 * @return null
	 */
	public function addBundle($bundle): null
	{
		return WebSiteFactory::addBundle($bundle);
	}

	/**
	 * @return Enclave
	 */
	public function enclave(): Enclave
	{
		return new Enclave();
	}

	/**
	 * @return Fragment
	 */
	public function fragment(): Fragment
	{
		return new Fragment();
	}

	/**
	 * @return void
	 */
	public function clearMain(): void
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
