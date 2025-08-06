<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
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

	/**
	 * @param array $params
	 * @return bool
	 */
	public function edit(array $params): bool
	{
		$idcreativeWork = $params['idcreativeWork'] ?? null;
		if($idcreativeWork) {
			$this->data = CmsFactory::model()->api()->get('creativeWork', ['idcreativeWork' => $idcreativeWork, 'properties'=>'isPartOf'])->ready();
		}
		return parent::edit($params);
	}
}
