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
				$data = null;
			}
			return CmsFactory::view()->webSite()->type('invoice')->setData(['provider'=>$providerData,'invoices'=>$data])->ready();
		}
		return CmsFactory::view()->webSite()->type('invoice')->ready();
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
		$idinvoice = $params['idinvoice'] ?? null;
		if ($idinvoice) {
			$dataInvoice = CmsFactory::model()->type('invoice')->get(['idinvoice'=>$idinvoice, 'properties'=>'customer,provider,referencesOrder']);
			if (isset($dataInvoice[0])) {
				return CmsFactory::view()->webSite()->type('invoice')->setData($dataInvoice[0])->setMethodName('edit')->ready();
			}
		}
		return false;
	}
}
