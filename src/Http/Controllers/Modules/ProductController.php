<?php
namespace Plinct\Cms\Http\Controllers\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Application\Modules\ModuleUseCase;
use Plinct\Cms\Http\View\Modules\Product\ProductListView;
use Plinct\Cms\Http\View\Modules\Product\ProductShowView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ProductController
{
	public function __construct(private ProductListView $view, private ModuleUseCase $moduleUseCase, private ProductShowView $showView)
	{
	}

	public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->view->setContext($context);
		$this->view->build();
		$response->getBody()->write($this->view->render());
		return $response;
	}

	/**
	 * @throws GuzzleException
	 */
	public function edit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$data = $this->moduleUseCase->show('product',['idproduct'=>$request->getAttributes()['id']]);
		$this->showView->setContext($context);
		if ($data['status'] === true) {
			$this->showView->build($data['data'][0]);
		} else {
			$this->showView->warning($data['message']);
		}
		$response->getBody()->write($this->showView->render());
		return $response;
	}

	public function new(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->showView->setContext($context);
		$this->showView->new();
		$response->getBody()->write($this->showView->render());
		return $response;
	}
}
