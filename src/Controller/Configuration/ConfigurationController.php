<?php
namespace Plinct\Cms\Controller\Configuration;

use Plinct\Cms\CmsFactory;

class ConfigurationController
{
	/**
	 * @var array
	 */
	private static array $modulesEnabled = [];
	/**
	 * @var array
	 */
	private static array $modulesAvailabled = [];

	/**
	 * @param array $types
	 */
	public function setModulesEnabled(array $types): void
	{
		self::$modulesEnabled = $types;
	}

	/**
	 * @param array $modulesAvailabled
	 */
	public function setModulesAvailabled(array $modulesAvailabled): void
	{
		self::$modulesAvailabled = $modulesAvailabled;
	}

	/**
	 * @param string $moduleName
	 * @return bool
	 */
	public function hasModulesEnabled(string $moduleName): bool
	{
		return in_array($moduleName, self::$modulesEnabled);
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
		return in_array($moduleName, self::$modulesAvailabled);
	}

	/**
	 * @return array
	 */
	public function getModulesAvailable(): array
	{
		return self::$modulesAvailabled;
	}

	/**
	 * @return void
	 */
	public function index(): void
	{
		CmsFactory::view()->webSite()->configuration()->index(self::$modulesAvailabled, self::$modulesEnabled);
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