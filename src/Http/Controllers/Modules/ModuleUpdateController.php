<?php
namespace Plinct\Cms\Http\Controllers\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Modules\ModuleUpdateUseCase;
use Plinct\Cms\Http\Support\SupportHttp;
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
		$httpReferer = strtok($_SERVER['HTTP_REFERER'],'?');
		if ($dataUseCase['status'] === true) {
			$location = $httpReferer;
		} else {
			$location = $httpReferer . "?wrnc=". SupportHttp::encodeCript(urlencode($dataUseCase['message']));
		}
		return $response->withHeader('Location', $location)->withStatus(302);
	}
}
