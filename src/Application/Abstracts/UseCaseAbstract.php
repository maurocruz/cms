<?php
namespace Plinct\Cms\Application\Abstracts;

use Plinct\Cms\Application\Contracts\UseCaseInterface;
use Plinct\Cms\Application\Support\SupportApplication;

class UseCaseAbstract implements UseCaseInterface
{

	public function isSuccess(array $responseData): bool
	{
		if (isset($responseData['status']) && ($responseData['status']=='success' || $responseData['status'] === true)) {
			return true;
		}
		return false;
	}

	public function returnData(string $moduleName, array $dataApi): array
	{
		return SupportApplication::returnsModules($moduleName, $dataApi);
	}
}
