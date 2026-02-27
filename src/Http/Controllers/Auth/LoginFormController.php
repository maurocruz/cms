<?php
namespace Plinct\Cms\Http\Controllers\Auth;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Auth\LoginView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class LoginFormController
{
	public function __construct(private LoginView $view)
	{
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		if ($context->getUser()) {
			return $response->withHeader("Location", "/admin")->withStatus(302);
		}
		$this->view->setContext($context);
		$this->view->index();
		$response->getBody()->write($this->view->render());
		return $response;
	}
}
