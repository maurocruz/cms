<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;
use Plinct\Cms\View\WebSite\Structure\Structure;
use Plinct\Tool\Locale;
use Plinct\Web\Render;

class WebSiteFactory extends WebSiteFactoryAbstract
{
	/**
	 * @return null
	 */
	public function create()
	{
		// LANGUAGE
		self::$HTML['attributes'] = ["lang" => Locale::getServerLanguage()];
		// TRANSLATE BY GETTEXT
		Locale::translateByGettext(App::getLanguage(), "plinctCms", App::getBASEDIR()."/Locale");
		// HEAD
		parent::addHead(Structure::head());
		return null;
	}

	/**
	 * @return void
	 */
	public static function buildBodyStructure()
	{
		// HEADER
		if (CmsFactory::controller()->user()->userLogged()->getIduser()) {
			parent::addHeader(Structure::mainMenu(), true);
		}
		parent::addHeader(Structure::header(), true);
		if (CmsFactory::controller()->user()->userLogged()->getIduser()) {
			parent::addHeader(Structure::userBar(), true);
		}
		// FOOTER
		parent::addFooter(Structure::footer());
	}

	public static function clearMain()
	{
		parent::$MAIN['content'] = null;
	}

	/**
	 * @return string
	 */
	public static function ready(): string
	{
		parent::$CONTENT['content'][] = self::$HEADER;
		parent::$CONTENT['content'][] = self::$MAIN;
		parent::$CONTENT['content'][] = self::$FOOTER;
		parent::$BODY['content'][] = self::$CONTENT;
		// HEAD
		parent::$HEAD['content'][] = '<script>window.apiHost = "'.App::getApiHost().'"; window.staticFolder = "'.App::getStaticFolder().'";</script>';

		// BODY BUNDLES
		parent::$BODY['content'][] = /** @lang text */
			'<script src="/admin/assets/js/scripts"></script><script src="https://plinct.com.br/static/dist/plinct-shell/main(v3).js"></script>';
		parent::$BODY['content'][] = '<script src="'.App::getStaticFolder().'index.bundle.js" data-apiHost="'.App::getApiHost().'" data-staticFolder="'.App::getStaticFolder().'"></script>';
		foreach (parent::$BUNDLES as $bundle) {
			parent::$BODY['content'][] = '<script src="'.App::getStaticFolder().$bundle.'.bundle.js"></script>';
		}

		parent::$HTML['content'][] = self::$HEAD;
		parent::$HTML['content'][] = self::$BODY;
		// RETURN
		return "<!DOCTYPE html>" . Render::arrayToString(parent::$HTML);
	}
}