<?php

declare(strict_types=1);

namespace Plinct\Cms\Middleware;

class Middleware
{
	public function authentication(): AuthenticationMiddleware
	{
		return new AuthenticationMiddleware();
	}

	public function gateway(): GatewayMiddleware
	{
		return new GatewayMiddleware();
	}
}