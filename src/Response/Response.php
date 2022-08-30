<?php

declare(strict_types=1);

namespace Plinct\Cms\Response;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Response\Fragment\Fragment;
use Plinct\Cms\Response\Message\Message;
use Plinct\Cms\Response\View\View;
use Plinct\Cms\WebSite\WebSite;
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

	public function isUserLogged(callable $callable)
	{
		if (CmsFactory::request()->user()->userLogged()->getIduser()) {
			$callable();
		} else {
			CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->auth()->login()
			);
		}
	}
	/**
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function writeBody(ResponseInterface $response): ResponseInterface
	{
		$response->getBody()->write($this->webSite()->ready());
		return $response;
	}
}
