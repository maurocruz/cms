<?php
namespace Plinct\Cms\Application\Support;

class SupportApplication
{
	/**
	 * @param string $moduleName
	 * @param array $data
	 * @return array
	 */
	public static function returnsModules(string $moduleName, array $data): array
	{
		if (isset($data[0])) {
			return ['status'=>true, 'message' => "Module $moduleName found", 'data'=>$data];
		} elseif (empty($dataApi)) {
			return ['status' => false, 'message' => "Module $moduleName not found", 'data' => $data];
		}
		return ['status' => false, 'message' => 'Error', 'data' => $dataApi];

	}

	public static function returnsActions(string $action, array $data): array
	{
		if (isset($data['status']) && $data['status'] === 'success') {
			return ['status'=>true, 'message'=>"Module $action successfully", 'data'=>$data['data'][0]];
		} else {
			return ['status'=>false, 'message'=>$data['message']];
		}
	}

}
