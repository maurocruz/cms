<?php
namespace Plinct\Cms\Controller\Type\Intangible;

use Exception;
use Plinct\Cms\CmsFactory;

class InvoiceController
{

	/**
	 * @throws Exception
	 */
	public function index(array $params): bool
	{
		$provider = $params['provider'] ?? null;
		if ($provider) {
			$dataProvider = CmsFactory::model()->type('thing')->get(['idthing'=>$provider,'hasPart'=>true]);
			return CmsFactory::view()->webSite()->type('invoice')->setData($dataProvider[0] ?? null)->setQueryParams($params)->ready();
		}
		return CmsFactory::view()->webSite()->type('invoice')->ready();
	}
}
