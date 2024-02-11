<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Response\View;

use Plinct\Cms\Controller\Response\View\User\User;

class View
{
	/**
	 * @return User
	 */
	public function user(): User {
		return new User();
	}
}
