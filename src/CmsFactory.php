<?php
declare(strict_types=1);
namespace Plinct\Cms;

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Exception;
use Plinct\Cms\Controller\Controller;
use Plinct\Cms\Helpers\Helpers;
use Plinct\Cms\Model\Model;
use Plinct\Cms\View\View;
use Plinct\Tool\ToolBox;
use Slim\App;

class CmsFactory
{
	/**
	 * @throws Exception
	 */
  public static function create(array $settings = []): App
  {
		// CONTAINER
		$builder = new ContainerBuilder();
		$builder->addDefinitions(__DIR__.'/Container/container.php');
		// BASEDIR
	  $settings['basedir'] = realpath(__DIR__.'/../');
		// ADD SETTINGS
		$builder->addDefinitions(['settings' => $settings]);
		// BUILD CONTAINER
		$container = $builder->build();

		// SLIM APP
		$slimApp = Bridge::create($container);
		if (isset($settings['debug']) && $settings['debug']) {
			error_reporting(E_ALL);
			$slimApp->addErrorMiddleware(true, true, true);
		}
		// ROUTES
	  (require __DIR__ . '/Http/routes.php')($slimApp);
		//
		return $slimApp;
  }

	/**
	 * @return Controller
	 */
	public static function controller(): Controller {
		return new Controller();
	}

	/**
	 * @return Helpers
	 */
	public static function helpers(): Helpers
	{
		return new Helpers();
	}

	/**
	 * @return Model
	 */
	public static function model(): Model {
		return new Model();
	}

	/**
	 * @return ToolBox
	 */
	public static function toolBox(): ToolBox
	{
		return new ToolBox();
	}
	/**
	 * @return View
	 */
	public static function view(): View {
		return new View();
	}
}
