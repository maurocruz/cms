<?php
namespace Plinct\Cms\Http\Controllers\Config;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Config\ConfigUseCase;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Config\ConfigView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpMethodNotAllowedException;

readonly class ConfigController
{

	public function __construct(private ConfigView $view, private ConfigUseCase $configUseCase)
	{
	}

	public function dashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$this->view->setContext($context);
		$this->view->index();
		$response->getBody()->write($this->view->render());
		return $response;
	}

	/**
	 * @throws GuzzleException
	 */
	public function installModule(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		$moduleName = $params['moduleName'] ?? null;
		$responseApi = $this->configUseCase->installModule($moduleName);
		if (isset($responseApi['status']) && $responseApi['status'] === 405) {
			throw new HttpMethodNotAllowedException($request);
		} elseif ($responseApi['status'] === 'success') {
			$location = "/admin/config?ntc=".$responseApi['message'];
		} else {
			$location = "/admin/config?wrn=".$responseApi['message'];
		}
		return $response->withHeader('Location', $location)->withStatus(302);
	}
}
