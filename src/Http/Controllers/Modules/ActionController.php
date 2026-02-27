<?php
namespace Plinct\Cms\Http\Controllers\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Application\Modules\ActionUseCase;
use Plinct\Cms\Http\Controllers\Abstracts\ControllerAbstract;
use Plinct\Cms\Http\View\Modules\Action\ActionView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ActionController extends ControllerAbstract
{
	public function __construct(private readonly ActionView $view, private readonly ActionUseCase $actionUseCase)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->view->setContext($context);
		$dataUseCase = $this->actionUseCase->withObject($request->getQueryParams());
		$this->view->index($dataUseCase);
		$response->getBody()->write($this->view->render());
		return $response;
	}

	/**
	 * @throws GuzzleException
	 */
	public function edit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$id = $request->getAttributes()['id'];
		$queryParams = $request->getQueryParams();
		$this->view->setContext($context);
		// USE CASE
		$dataUseCase = $this->actionUseCase->show($id, $queryParams);
		// VIEW
		$this->returnEditClause($this->view, $dataUseCase);
		$response->getBody()->write($this->view->render());
		return $response;
	}

	/**
	 * @throws GuzzleException
	 */
	public function new(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$queryParams = $request->getQueryParams();
		$this->view->setContext($context);
		$dataUseCase = $this->actionUseCase->withObject($queryParams);
		$this->view->new($dataUseCase);
		$response->getBody()->write($this->view->render());
		return $response;
	}
}
