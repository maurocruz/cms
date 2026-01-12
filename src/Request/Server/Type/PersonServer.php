<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Request\Server\Type;

class PersonServer
{
  public function edit($params)
  {
    if ($params['birthDate'] == '') {
      unset($params['birthDate']);
    }
		return $params;
  }
}