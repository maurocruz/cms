<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Authentication;

use Plinct\Tool\ToolBox;

class Authentication
{
	public function register($data)
	{
		// LOGGED
		$logger = ToolBox::Logger('auth', 'auth.log');
		if (isset($data['status'])) {
			if ($data['status'] === 'fail') {
				$logger->info("REGISTER FAILED: " . $data['message'], ['email' => $params['email']]);
			}
			if ($data['status'] === 'error') {
				$message = isset($data['data']['error']) ? $data['data']['error']['message'] : $data['message'];
				$logger->info("REGISTER ERROR: ".$message);
			}
			if ($data['status'] === 'success') {
				$logger->info("REGISTER SUCCESS: ".$data['message'], $data['data']);
			}
		} else if ($data === null) {
			$logger->critical("REGISTER FAILED: Api return is null");
		}
		// RETURN
		return $data;
	}
}