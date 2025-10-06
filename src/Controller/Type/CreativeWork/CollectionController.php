<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Plinct\Cms\Controller\Type\TypeControllerInterface;

class CollectionController extends CreativeWorkController implements TypeControllerInterface
{
	/**
	 * @param string $type
	 */
	public function __construct(string $type = 'collection')
	{
		parent::__construct($type);
	}
}
