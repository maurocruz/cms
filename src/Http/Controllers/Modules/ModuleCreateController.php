<?php
namespace Plinct\Cms\Http\Controllers\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Modules\ModuleCreateUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ModuleCreateController
{
	public function __construct(private ModuleCreateUseCase $moduleCreateUseCase)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$type = $request->getAttributes()['type'];
		$params = $request->getParsedBody();
		$dataUseCase = $this->moduleCreateUseCase->create($type, $params);
		if ($dataUseCase['status'] === true) {
			$location = "/admin/".$type."/".$dataUseCase['data']['id'.$type];
		} else {
			$location = "/admin/".$type."/new?wrn=".urlencode($dataUseCase['message']);
		}
		return $response->withHeader('Location', $location)->withStatus(302);
	}



}
