<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Configuration;

use Plinct\Cms\CmsFactory;

class Configuration
{
	/**
	 * @return string[]
	 */
	public function initApplication(): array
	{
		$data = CmsFactory::model()->api()->get('config/database', ['schema'=>'basic'])->ready();
		if ($data['status'] === 'success') {
			CmsFactory::view()->Logger('config','info.log')->info('Created Schema', $data);
			return ['status'=>'success', 'message'=>'SQL schema basic has been builded' ];
		} else {
			CmsFactory::view()->Logger('config','info.log')->info('SQL Schema fail', $data);
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_($data['message'])));
			return ['status'=>'fail', 'message'=>'SQL schema basic was not builded'];
		}
	}
}