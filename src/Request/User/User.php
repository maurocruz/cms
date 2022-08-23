<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\User;

use Plinct\Cms\CmsFactory;

class User
{
	public function get(array $params = []) {
		return CmsFactory::server()->api()->get('user', $params)->ready();
	}
	public function userLogged(): UserLogged {
		return new UserLogged();
	}
}