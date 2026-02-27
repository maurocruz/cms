<?php
namespace Plinct\Cms\Http\Controllers\Auth;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Auth\RegisterFormView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class RegisterFormController
{
	public function __construct(private RegisterFormView $view)
	{
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->view->setContext($context);
		$this->view->index();
		$response->getBody()->write($this->view->render());
		return $response;
	}
}
