<?php
namespace Plinct\Cms\Http\ExceptionHandlers;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Auth\LoginView;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Throwable;

readonly class HttpNotFoundHandler
{

	public function __construct(private LoginView $loginView)
	{
	}

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): Response|ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->loginView->setContext($context);
		$response = new Response();
		$response->withStatus(404);
		if (!$context->getUser()) {
			$this->loginView->build();
		} else {
			$this->loginView->warning('Page not found!');
		}
		$response->getBody()->write($this->loginView->render());
		return $response;
	}
}
