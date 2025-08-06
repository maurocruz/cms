<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
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

	/**
	 * @param array $params
	 * @return bool
	 */
	public function edit(array $params): bool
	{
		$idcollection = $params['idcollection'] ?? null;
		if($idcollection) {
			$this->data = CmsFactory::model()->api()->get('collection', ['idcollection' => $idcollection, 'properties'=>'hasPart'])->ready();
		}
		return parent::edit($params);
	}
}
