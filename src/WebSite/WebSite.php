<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Enclave\Enclave;
use Plinct\Cms\WebSite\Type\Controller;
use Plinct\Cms\WebSite\Type\Type;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Cms\WebSite\Structure\Structure;
use Plinct\Tool\Locale;
use Plinct\Web\Render;
use ReflectionException;

class WebSite extends WebSiteAbstract
{
  /**
   * @return void
   */
  public function create()
  {
    if (session_status() === PHP_SESSION_NONE) session_start();
    // LANGUAGE
    self::$HTML['attributes'] = ["lang" => Locale::getServerLanguage()];
    // TRANSLATE BY GETTEXT
    Locale::translateByGettext(App::getLanguage(), "plinctCms", __DIR__."/../../Locale");
    // HEAD
    parent::addHead(Structure::head());
  }

	/**
	 * @return void
	 */
	public function buildBodyStructure()
	{
		// HEADER
		if (CmsFactory::request()->user()->userLogged()->getIduser()) { parent::addHeader(Structure::mainMenu(), true); }
		parent::addHeader(Structure::header(), true);
		if (CmsFactory::request()->user()->userLogged()->getIduser()) { parent::addHeader(Structure::userBar(), true);}
		// FOOTER
		parent::addFooter(Structure::footer());
	}
	/**
	 * @param string $bundle
	 * @return void
	 */
	final public function addBundle(string $bundle)
	{
		if (in_array($bundle,parent::$BUNDLES) === false) {
			parent::$BUNDLES[] = $bundle;
		}
	}

	public function clearMain()
	{
		parent::$MAIN['content'] = null;
	}

	/**
	 * @return Enclave
	 */
	public static function enclave(): Enclave {
		return new Enclave();
	}

	/**
	 * @throws ReflectionException
	 */
	public function getContent(array $params = null, array $queryStrings = null)
	{
		$type = $queryStrings['type'] ?? $params['type'] ?? null;
		$methodName =  $params['methodName'] ?? $queryStrings['part'] ?? $queryStrings['action'] ?? 'index';
		$id = $queryStrings['id'] ?? $params['id'] ?? null;

		if($id && $methodName == 'index') $methodName = 'edit';

		if ($type) {
			$controller = new Controller();
			$data = $controller->getData($type, $methodName, $id, $queryStrings);

			if (isset($data['status']) && $data['status'] == 'fail') {
				CmsFactory::response()->webSite()->addMain(
					CmsFactory::response()->message()->warning($data['message'])
				);
			} else {
				$view = new View();
				$allParams = array_merge($params, $queryStrings);
				$view->view($type, $methodName, $data, $allParams);
			}

		} else {
			parent::addMain("<p>Control Panel CMSCruz - version " . App::getVersion() . ".</p>" );
		}
	}

	/**
	 * @param string|null $title
	 * @param array|null $list
	 * @param int|null $level
	 * @param array|null $searchInput
	 */
	public function navbar(string $title = null, array $list = null, int $level = null, array $searchInput = null)
	{
		$fragment = CmsFactory::response()->fragment()
			->navbar()
			->title($title)
			->level($level);
		if ($list) {
			foreach ($list as $key => $value) {
				$fragment->newTab($key, $value);
			}
		}

		if ($searchInput) {
			$type = $searchInput['table'] ?? null;
			if($type) $fragment->type($type);
			$fragment->search("/admin/$type/search",$searchInput['searchBy'] ?? "name", $searchInput['params'] ?? null, $searchInput['linkList'] ?? null);
		}

		$this->addHeader($fragment->ready());
	}


  /**
   * @return string
   */
  public function ready(): string
  {
    parent::$CONTENT['content'][] = self::$HEADER;
    parent::$CONTENT['content'][] = self::$MAIN;
    parent::$CONTENT['content'][] = self::$FOOTER;
    parent::$BODY['content'][] = self::$CONTENT;
		// HEAD
    parent::$HEAD['content'][] = '<script>window.apiHost = "'.App::getApiHost().'"; window.staticFolder = "'.App::getStaticFolder().'";</script>';

		// BODY BUNDLES
	  parent::$BODY['content'][] = '<script src="'.App::getStaticFolder().'js/dist/index.bundle.js" data-apiHost="'.App::getApiHost().'" data-staticFolder="'.App::getStaticFolder().'"></script>';
	  foreach (parent::$BUNDLES as $bundle) {
		  parent::$BODY['content'][] = '<script src="'.App::getStaticFolder().'js/dist/'.$bundle.'.bundle.js"></script>';
	  }

    parent::$HTML['content'][] = self::$HEAD;
    parent::$HTML['content'][] = self::$BODY;
    // RETURN
    return "<!DOCTYPE html>" . Render::arrayToString(parent::$HTML);
  }

	/**
	 * @param string $type
	 * @return Type
	 */
	public function type(string $type): Type
	{
		return new Type($type);
	}

	/**
	 * @param string $message
	 * @return void
	 */
	public function warning(string $message) {
		$this->addMain([ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => $message ]);
	}
}
