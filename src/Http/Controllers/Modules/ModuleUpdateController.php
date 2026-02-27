<?php
namespace Plinct\Cms\Http\Controllers\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Modules\ModuleUpdateUseCase;
use Plinct\Cms\Http\SupportHttp\HttpSupport;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ModuleUpdateController
{
	public function __construct(private ModuleUpdateUseCase $moduleUpdateUseCase)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$type = $request->getAttributes()['type'];
		$params = $request->getParsedBody();
		$dataUseCase = $this->moduleUpdateUseCase->update($type, $params);
		if ($dataUseCase['status'] === true) {
			$location = strstr($request->getServerParams()['HTTP_REFERER'],'?',true);
		} else {
			$location = $_SERVER['HTTP_REFERER'] . "?wrnc=". HttpSupport::encodeCript(urlencode($dataUseCase['message']));
		}
		return $response->withHeader('Location', $location)->withStatus(302);
	}
}
