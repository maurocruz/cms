<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Plinct\Cms\Controller\Type\ThingController;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class CreativeWorkController extends ThingController implements TypeControllerInterface
{
	/**
	 * @param string $type
	 */
	public function __construct(string $type = 'creativeWork')
	{
		parent::__construct($type);
	}
}
