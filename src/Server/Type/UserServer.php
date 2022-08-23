<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Request\Api;

class UserServer
{
	/**
	 * @param array $params
	 * @return string
	 */
  public function new(array $params): ?string
  {
    // API
    $data = Api::post('user', $params);

    if(isset($data['id'])) {
      return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
    } elseif(isset($data['status'])) {
      return filter_input(INPUT_SERVER, 'HTTP_REFERER') . "?" . http_build_query($data);
    }
		return null;
  }
}
