<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class ImageObjectController extends MediaObjectController implements TypeControllerInterface
{
	/**
	 * @param string $type
	 */
	public function __construct(string $type = 'imageObject')
	{
		parent::__construct($type);
	}

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
	public function edit(array $params): bool
	{
		$dataImageObject = CmsFactory::model()->api()->get('imageObject',$params)->ready();
		return CmsFactory::view()->webSite()->type('imageObject')->setData($dataImageObject)->setMethodName('edit')->ready();
	}
}
