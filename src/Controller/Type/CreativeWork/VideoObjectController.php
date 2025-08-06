<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class VideoObjectController extends MediaObjectController implements TypeControllerInterface
{
	/**
	 * @param string $type
	 */
	public function __construct(string $type = 'videoObject')
	{
		parent::__construct($type);
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('videoObject')->setMethodName('index')->ready();
	}

	/**
	 * @throws Exception
	 */
	public function edit(array $params): bool
	{
		$data = CmsFactory::model()->type('videoObject')->get($params);
		return CmsFactory::view()->webSite()->type('videoObject')->setData($data)->setMethodName('edit')->ready();
	}
}
