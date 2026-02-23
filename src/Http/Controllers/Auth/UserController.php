<?php
namespace Plinct\Cms\Http\Controllers\Auth;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Template\Template;
use Plinct\Cms\Http\View\ViewFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController
{
	private ViewFactory $view;
	private Template $template;

	public function __construct(ViewFactory $view, Template $template)
	{
		$this->view = $view;
		$this->template = $template;
	}

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$user = $context->getUser();
		$this->template->setContext($context);

		$this->template->addMain("<h1>User page</h1>");
		$this->template->render($response);

		return $response;
	}

}
