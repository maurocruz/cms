<?php
namespace Plinct\Cms\Http\Controllers\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Application\Modules\ActionUseCase;
use Plinct\Cms\Http\View\Modules\Action\ActionListView;
use Plinct\Cms\Http\View\Modules\Action\ActionShowView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ActionController
{
	public function __construct(private ActionListView $listView, private ActionUseCase $actionUseCase, private ActionShowView $showView)
	{
	}

	public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->listView->setContext($context);
		$this->listView->build();
		$response->getBody()->write($this->listView->render());
		return $response;
	}

	/**
	 * @throws GuzzleException
	 */
	public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$id = $request->getAttributes()['id'];
		$queryParams = $request->getQueryParams();
		$this->showView->setContext($context);
		$dataUseCase = $this->actionUseCase->show($id, $queryParams);
		if ($dataUseCase['status']) {
			$this->showView->build($dataUseCase['data']);
		} else {
			var_dump($dataUseCase['message']);
		}
		$response->getBody()->write($this->showView->render());
		return $response;
	}
}
