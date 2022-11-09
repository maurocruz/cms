<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;

class UserServer
{
	/**
	 * @param array $params
	 * @return string|void
	 */
  public function new(array $params)
  {
    // API
    $data = Api::post('user', $params);

    if(isset($data['id'])) {
        return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
    } elseif(isset($data['status'])) {
        return filter_input(INPUT_SERVER, 'HTTP_REFERER') . "?" . http_build_query($data);
    }
		print_r($data);
		die();
  }

	/**
	 * @param array $params
	 * @return mixed|void
	 */
	public function edit(array $params)
	{
		$data = Api::put('user',$params);
		if ($data['status'] == "success") {
			return filter_input(INPUT_SERVER,'HTTP_REFERER');
		}
		print_r($data);
		die();
	}

	/**
	 * @param array $params
	 * @return string|void
	 */
	public function erase(array $params)
	{
		if (isset($params['iduser'])) {
			$data = Api::delete('user', ['iduser'=>$params['iduser']]);
			if ($data['status'] == 'success') {
				return "/admin/user";
			}
		}
		print_r(__FILE__);
		print_r($data);
		die();
	}
}
