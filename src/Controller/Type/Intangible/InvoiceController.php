<?php
namespace Plinct\Cms\Controller\Type\Intangible;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class InvoiceController implements TypeControllerInterface
{

	/**
	 * @inheritDoc
	 */
	public function index(array $params): bool
	{
		$provider = $params['provider'] ?? null;
		if ($provider) {
			$dataProvider = CmsFactory::model()->type('thing')->get(['idthing'=>$provider,'hasPart'=>true]);
			$providerData = $dataProvider[0] ?? null;
			if (array_key_exists('overdueInvoice',$params)) {
				$data = CmsFactory::model()->type('invoice')->get(['overdueInvoice' => true] + $params);
				return CmsFactory::view()->webSite()->type('invoice')->setMethodName('overdueInvoice')->setData(['provider'=>$providerData,'invoices'=>$data])->ready();
			} else {
				$data = null;// CmsFactory::model()->type('invoice')->get($params);
			}
			return CmsFactory::view()->webSite()->type('invoice')->setData(['provider'=>$providerData,'invoices'=>$data])->ready();
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function new(array $params): bool
	{
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function edit(array $params): bool
	{
		return false;
	}
}
