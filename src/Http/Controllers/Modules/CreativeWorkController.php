<?php
namespace Plinct\Cms\Http\Controllers\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Application\Modules\CreativeWorkUseCase;
use Plinct\Cms\Http\Controllers\Abstracts\ControllerAbstract;
use Plinct\Cms\Http\Controllers\Contracts\ModuleControllerInterface;
use Plinct\Cms\Http\View\Modules\CreativeWork\CreativeWorkView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreativeWorkController extends ControllerAbstract
{
	public function __construct(private readonly CreativeWorkView $view, private readonly CreativeWorkUseCase $creativeWorkUseCase)
	{
	}

	public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->view->setContext($context);
		$this->view->index();
		$response->getBody()->write($this->view->render());
		return $response;
	}

	public function new(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->view->setContext($context);
		$this->view->new();
		$response->getBody()->write($this->view->render());
		return $response;
	}

	/**
	 * @throws GuzzleException
	 */
	public function edit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->view->setContext($context);
		$data = $this->creativeWorkUseCase->findItem($request->getAttributes()['id']);
		$this->returnEditClause($this->view, $data);
		$response->getBody()->write($this->view->render());
		return $response;
	}

}
