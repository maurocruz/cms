<?php

declare(strict_types=1);

namespace Plinct\Cms\Response\View;

use Plinct\Cms\Response\View\User\User;

class View
{
	/**
	 * @return User
	 */
	public function user(): User {
		return new User();
	}
}
