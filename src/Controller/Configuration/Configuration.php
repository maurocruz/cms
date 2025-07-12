<?php
namespace Plinct\Cms\Controller\Configuration;

use Plinct\Cms\CmsFactory;

class Configuration
{

	/**
	 * @var array
	 */
	private static array $modulesEnabled = [];
	/**
	 * @var array
	 */
	private static array $modulesAvailable = [];

	/**
	 * @param array $types
	 */
	public function setModulesEnabled(array $types): void
	{
		self::$modulesEnabled = $types;
	}

	/**
	 * @param array $modulesAvailable
	 */
	public function setModulesAvailable(array $modulesAvailable): void
	{
		self::$modulesAvailable = $modulesAvailable;
	}

	/**
	 * @return array
	 */
	public function getModulesEnabled(): array
	{
		return self::$modulesEnabled;
	}

	/**
	 * @param string $moduleName
	 * @return bool
	 */
	public function hasModulesAvailable(string $moduleName): bool
	{
		return in_array($moduleName, self::$modulesAvailable);
	}

	/**
	 * @return array
	 */
	public function getModulesAvailable(): array
	{
		return self::$modulesAvailable;
	}

	/**
	 * @return void
	 */
	public function index(): void
	{
		CmsFactory::view()->webSite()->configuration()->index(self::$modulesAvailable, self::$modulesEnabled);
	}

	/**
	 * @return void
	 */
	public function sitemap(): void
	{
		CmsFactory::view()->webSite()->configuration()->sitemap();
	}

	/**
	 * @param string $module
	 * @return string[]
	 */
	public function installModule(string $module): array
	{
		$data = CmsFactory::model()->api()->post('config/installModule',['module'=>$module])->ready();
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