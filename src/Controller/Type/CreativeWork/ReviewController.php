<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Plinct\Cms\CmsFactory;

class ReviewController extends CreativeWorkController
{

	public function __construct(string $type = 'review')
	{
		parent::__construct($type);
	}

	/**
	 * @inheritDoc
	 */
	public function edit(array $params): bool
	{
		$this->data = CmsFactory::model()->api()->get('review', $params)->ready();
		return CmsFactory::view()->webSite()->type('review')->setMethodName('edit')->setData($this->data)->ready();
	}
}
