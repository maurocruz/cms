<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class MediaObjectController extends CreativeWorkController implements TypeControllerInterface
{
	/**
	 * @param string $type
	 */
	public function __construct(string $type = 'mediaObject')
	{
		parent::__construct($type);
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function edit(array $params): bool
	{
		$this->data = CmsFactory::model()->api()->get('mediaObject', ['properties'=>'isPartOf'] + $params)->ready();
		return CmsFactory::view()->webSite()->type('mediaObject')->setMethodName('edit')->setData($this->data)->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function hasPart(array $params): bool
	{
		$idHasPart = $params['idHasPart'] ?? null;
		if ($idHasPart) {
			$this->data = CmsFactory::model()->api()->get('mediaObject', ['thing' => $idHasPart, 'properties'=>'hasPart'])->ready();
		}
		return CmsFactory::view()->webSite()->type('mediaObject')->setMethodName('hasPart')->setData($this->data)->setQueryParams($params)->ready();
	}
}
