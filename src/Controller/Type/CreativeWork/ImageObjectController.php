<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class ImageObjectController implements TypeControllerInterface
{

	/**
	 * @inheritDoc
	 */
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('imageObject')->setMethodName('index')->ready();
	}

	/**
	 * @inheritDoc
	 */
	public function new(array $params): bool
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function edit(array $params): bool
	{
		$idimageObject = $params['idimageObject'] ?? null;
		if($idimageObject) {
			$dataImageObject = CmsFactory::model()->api()->get('imageObject', ['idimageObject' => $idimageObject, 'properties'=>'mentions'])->ready();
			return CmsFactory::view()->webSite()->type('imageObject')->setData($dataImageObject)->setMethodName('edit')->ready();
		}
		return true;
	}
}
