<?php
namespace Plinct\Cms\Http\Controllers\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Application\Modules\ActionUseCase;
use Plinct\Cms\Http\View\Modules\Action\ActionIndexView;
use Plinct\Cms\Http\View\Modules\Action\ActionNewView;
use Plinct\Cms\Http\View\Modules\Action\ActionEditView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ActionController
{
	public function __construct(private ActionIndexView $listView, private ActionUseCase $actionUseCase, private ActionEditView $showView, private ActionNewView $newView)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->listView->setContext($context);
		$dataUseCase = $this->actionUseCase->withObject($request->getQueryParams());
		$this->listView->build($dataUseCase);
		$response->getBody()->write($this->listView->render());
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
		$this->showView->setContext($context);
		$dataUseCase = $this->actionUseCase->show($id, $queryParams);
		if ($dataUseCase['status']) {
			$this->showView->build($dataUseCase['data'][0] ?? []);
		} else {
			var_dump($dataUseCase['message']);
		}
		$response->getBody()->write($this->showView->render());
		return $response;
	}

	/**
	 * @throws GuzzleException
	 */
	public function new(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$queryParams = $request->getQueryParams();
		$this->newView->setContext($context);
		$dataUseCase = $this->actionUseCase->withObject($queryParams);
		$this->newView->build($dataUseCase);
		$response->getBody()->write($this->newView->render());
		return $response;
	}
}
