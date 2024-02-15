<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Type;

use Plinct\Cms\Controller\Type\ImageObject\ImageObject;

class Type
{
	public function imageObject(): ImageObject
	{
		return new ImageObject();
	}
}