<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Request\Server\Type;

use Plinct\Cms\Controller\CmsFactory;

class UserServer
{
	/**
	 * @param array $params
	 * @return string
	 */
  public function new(array $params): ?string
  {
    // API
    $data = CmsFactory::request()->api()->post('user', $params)->ready();

    if(isset($data['id'])) {
      return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . "edit" . DIRECTORY_SEPARATOR . $data['id'];
    } elseif(isset($data['status'])) {
      return filter_input(INPUT_SERVER, 'HTTP_REFERER') . "?" . http_build_query($data);
    }
		return null;
  }
}
