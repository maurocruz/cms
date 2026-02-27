<?php
namespace Plinct\Cms\Application\Support;

class SupportApplication
{
	/**
	 * @param array $data
	 * @return array
	 */
	public static function returnsModules(array $data): array
	{
		if (isset($data[0])) {
			return ['status'=>true, 'data'=>$data];
		} elseif (empty($dataApi)) {
			return ['status' => false, 'message' => 'Module not found', 'data' => $data];
		} else {
			return ['status' => false, 'message' => 'Error', 'data' => $dataApi];
		}
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
