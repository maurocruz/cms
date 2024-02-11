<?php
declare(strict_types=1);
namespace Plinct\Cms\View;

use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\logger\Logger;
use Plinct\Cms\View\WebSite\WebSite;
use Psr\Http\Message\ResponseInterface;

class View
{
	/**
	 * @return null
	 */
	public function createWebSite() {
		return (new WebSite())->create();
	}

	/**
	 * @param $content
	 * @return null
	 */
	public function addMain($content) {
		return WebSite::addMain($content);
	}

	/**
	 * @return Fragment
	 */
	public function fragment(): Fragment
	{
		return new Fragment();
	}

	/**
	 * @param string $channel
	 * @param string $filename
	 * @return Logger
	 */
	public function Logger(string $channel, string $filename = 'plinctCms.log'): Logger
	{
		return new Logger($channel, $filename);
	}
	/**
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function writeBody(ResponseInterface $response): ResponseInterface
	{
		WebSite::buildBodyStructure();
		$response->getBody()->write(WebSite::ready());
		return $response;
	}
}
