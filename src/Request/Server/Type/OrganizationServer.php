<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;

class OrganizationServer
{
  public function new($params): string {
    if (isset($params['additionalType'])) {
      $params['additionalType'] = str_replace(" -> ",",",$params['additionalType']);
    }
    // API
    $data = CmsFactory::request()->server()->api()->post("organization", $params)->ready();
    return App::getURL() . dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . "edit" . DIRECTORY_SEPARATOR . $data['id'];
  }

	/**
	 * @param $params
	 * @return mixed
	 */
  public function edit($params) {
    if (isset($params['additionalType'])) {
      $params['additionalType'] = str_replace(" -> ",",",$params['additionalType']);
    }
		return $params;
  }
}