<?php
namespace Plinct\Cms\Http\ExceptionHandlers;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Auth\LoginView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Throwable;

readonly class HttpUnauthorizedHandler
{

	public function __construct(private LoginView $loginView)	{

	}

	/**
	 */
	public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): Response|ResponseInterface
	{
		$pattern = $request->getUri()->getPath();
		$context = $request->getAttribute(RequestContext::class);
		$response = new Response();
		$response->withStatus(401);
		$view = $this->loginView;
		$view->setContext($context);
		if ($pattern != '/admin/') {
			$view->warning('Unauthorized access');
		}
		$view->build();
		$response->getBody()->write($view->render());
		return $response;
	}
}
