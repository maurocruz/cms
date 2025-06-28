<?php
declare(strict_types=1);
namespace Plinct\Cms;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\Controller;
use Plinct\Cms\Helpers\Helpers;
use Plinct\Cms\Model\Model;
use Plinct\Cms\View\View;

use Plinct\Tool\ToolBox;
use Slim\App as Slim;

class CmsFactory
{
  /**
   * @param Slim $slim
   * @return App
   */
  public static function create(Slim $slim): App {
		return new App($slim);
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
