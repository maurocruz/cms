<?php
namespace Plinct\Cms\Http\Controllers;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Layout\HomeView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class HomeController
{

	public function __construct(private HomeView $view)
	{
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function home(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$view = $this->view;
		$view->setContext($context);
		$view->index();
		$response->getBody()->write($view->render());
		return $response;
	}
}
