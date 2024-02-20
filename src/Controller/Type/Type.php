<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Type;

use Plinct\Cms\Model\Type\ImageObject\ImageObject;

class Type
{
	public function imageObject(): ImageObject
	{
		return new ImageObject();
	}
}