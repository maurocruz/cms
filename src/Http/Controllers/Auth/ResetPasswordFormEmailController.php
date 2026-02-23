<?php
namespace Plinct\Cms\Http\Controllers\Auth;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Auth\ResetPasswordSendEmailView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ResetPasswordFormEmailController
{
	public function __construct(private ResetPasswordSendEmailView $view)
	{
	}

	/**
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->view->setContext($context);
		$this->view->build();
		$response->getBody()->write($this->view->render());
		return $response;

	}
}
