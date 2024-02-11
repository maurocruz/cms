<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Response;

use Plinct\Cms\Controller\Response\Fragment\Fragment;
use Plinct\Cms\Controller\Response\Message\Message;
use Plinct\Cms\Controller\Response\View\View;
use Plinct\Cms\Controller\WebSite\WebSite;
use Psr\Http\Message\ResponseInterface;

class Response
{
	/**
	 * @return Fragment
	 */
	public function fragment(): Fragment
	{
		return new Fragment();
	}
	/**
	 * @return Message
	 */
	public function message(): Message
	{
		return new Message();
	}
	/**
	 * @return View
	 */
	public function view(): View {
		return new View();
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
		$this->webSite()->buildBodyStructure();
		$response->getBody()->write($this->webSite()->ready());
		return $response;
	}
}
