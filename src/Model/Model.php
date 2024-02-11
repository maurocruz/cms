<?php
declare(strict_types=1);
namespace Plinct\Cms\Model;

use Plinct\Cms\Model\Api\Api;

class Model
{
	public function Api(): Api
	{
		return new Api();
	}
}