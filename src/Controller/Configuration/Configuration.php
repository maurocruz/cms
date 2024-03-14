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
		if ($data['status'] === 'complete') {
			foreach ($data['data'] as $item) {
				CmsFactory::view()->Logger('database')->info('SUCCESS: Created table', $item);
			}
			return ['status'=>'success', 'message'=>'SQL schema basic has been builded' ];
		} else {
			CmsFactory::view()->Logger('database')->info('FAIL: SQL Schema fail', $data);
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_($data['message'])));
			return ['status'=>'fail', 'message'=>'SQL schema basic was not builded'];
		}
	}

	public function index(): void
	{
		CmsFactory::view()->webSite()->configuration()->index();
	}

	public function installModule(string $module): array
	{
		$data = CmsFactory::model()->api()->post('config/database',['installModule'=>$module])->ready();
		if ($data['status'] === 'success') {
			CmsFactory::view()->Logger('config')->info("SUCCESS: Module $module created", $data);
			return ['status'=>'success', 'message'=>"Module $module created" ];
		} else {
			CmsFactory::view()->Logger('config')->info('FAIL: Module has not created', $data);
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_($data['message'])));
			return ['status'=>'fail', 'message'=>'Module was not builded'];
		}
	}
}