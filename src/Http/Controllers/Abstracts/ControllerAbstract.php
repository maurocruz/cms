<?php
namespace Plinct\Cms\Http\Controllers\Abstracts;

use Plinct\Cms\Http\Controllers\Contracts\ModuleControllerInterface;
use Plinct\Cms\Http\View\Contracts\ModulesViewInterface;

abstract class ControllerAbstract implements ModuleControllerInterface
{
	public function returnEditClause(ModulesViewInterface $view, array $data): void
	{
		if ($data['status'] === true) {
			$view->edit($data['data'][0]);
		} else {
			$view->warning($data['message']);
		}
	}
}
