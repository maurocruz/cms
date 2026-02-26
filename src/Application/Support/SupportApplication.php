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
			return ['status' => false, 'message' => 'Module nota found', 'data' => $data];
		} else {
			return ['status' => false, 'message' => 'Error', 'data' => $dataApi];
		}
	}

}
