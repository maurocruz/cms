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
		$debug = $settings['debug'] ?? false;
		// CONTAINER
		$builder = new ContainerBuilder();
		$builder->addDefinitions(__DIR__.'/Container/container.php');
		// BASEDIR
	  $settings['basedir'] = realpath(__DIR__.'/../');
		if ($_ENV['MAIL_HOST']) $settings['mailHost'] = $_ENV['MAIL_HOST'];
		if ($_ENV['MAIL_USERNAME']) $settings['mailUsername'] = $_ENV['MAIL_USERNAME'];
		if ($_ENV['MAIL_PASSWORD']) $settings['mailPassword'] = $_ENV['MAIL_PASSWORD'];
		// ADD SETTINGS
		$builder->addDefinitions(['settings' => $settings]);
		// BUILD CONTAINER
		$container = $builder->build();

		// SLIM APP (middlewares and routes)
		$slimApp = Bridge::create($container);
		// MIDDLEWARES
	  (require __DIR__ . '/Http/Middleware/middlewares.php')($slimApp, $debug);
		// ROUTES
	  (require __DIR__ . '/Http/routes.php')($slimApp);
		// RETURN
		return $slimApp;
  }

	public static function controller(): Controller {
		return new Controller();
	}

	public static function helpers(): Helpers
	{
		return new Helpers();
	}

	public static function model(): Model {
		return new Model();
	}

	public static function toolBox(): ToolBox
	{
		return new ToolBox();
	}
	public static function view(): View {
		return new View();
	}
}
