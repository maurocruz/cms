<?php
namespace Plinct\Cms\View\WebSite\Structure;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;

class Structure
{
	/**
	 * @return string
	 */
	public static function head(): string
  {
		$cssReset = file_get_contents(__DIR__ . '/../../../../static/css/reset.css');
		$cssEstilos = file_get_contents(__DIR__ . '/../../../../static/css/estilos.css');
		$cssStyle = file_get_contents(__DIR__ . '/../../../../static/css/style.css');
		$cssStyleDark = file_get_contents(__DIR__ . '/../../../../static/css/style-dark.css');
		$jsScripts = file_get_contents(__DIR__ . '/../../../../static/js/scripts.js');

		$host = CmsFactory::controller()->getHost();

    $returns = '<meta charset="UTF-8">';
		$returns .= '<meta name="viewport" content="width=device-width">';
	  $returns .= "<link rel='shortcut icon' href='$host/favicon.ico' type='image/x-icon'>";
	  $returns .= '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">';
		$returns .= "<style>$cssReset</style>";
		$returns .= "<style>$cssEstilos</style>";
		$returns .= "<style>$cssStyle</style>";
		$returns .= "<style>$cssStyleDark</style>";
		$returns .= "<script>$jsScripts</script>";
		$returns .= '<link rel="stylesheet" href="https://plinct.com.br/static/dist/richtexteditor/rte_theme_default.css">';
	  $returns .= '<script type="text/javascript" src="https://plinct.com.br/static/dist/richtexteditor/rte.js"></script>';
	  $returns .= '<script type="text/javascript" src="https://plinct.com.br/static/dist/richtexteditor/plugins/all_plugins.js"></script>';
	  $returns .= '<script>
				const config = { toolbar: "mytoolbar", skin: "gray", url_base: "https://plinct.com.br/static/dist/richtexteditor", toggleBorder: false, showFloatParagraph: false };
        config.toolbar_mytoolbar = "{bold,italic,underline,strike,superscript,subscript}|{fontsize}|{forecolor,backcolor}|{justifyleft,justifycenter,justifyright,justifyfull}|{insertorderedlist,insertunorderedlist}|{insertlink,unlink,insertimage}|removeformat|insertdocument"
        +"#{undo,redo,fullscreenenter,fullscreenexit,code}";
			</script> ';
	  $returns .= '<script src="https://plinct.com.br/static/dist/plinct-shell/v3/main.js"></script>';
	  $returns .= '<title>Plinct CMS [' . App::getTitle() . ']</title>';
		$returns .= '<meta name="description" content="CMS for Plinct">';
		return $returns;
  }

  /**
   * @return string
   */
  public static function userBar(): string
  {
    $helloText = sprintf(_("Hello, %s."), CmsFactory::controller()->user()->userLogged()->getName());
    return "<div class='admin admin-bar-top'>
      <p>$helloText</p>
      <button class='button-link' onclick='navigator.clipboard.writeText(\"". CmsFactory::controller()->user()->userLogged()->getToken()."\")'>Copy token</button>
      <p><a href='/admin/logout'>" . _("Log out") . "</a></p>
    </div>";
  }

  /**
   * @return string
   */
  public static function header(): string
  {
    $apiHost = App::getApiHost();
    $apiLocation = $apiHost && filter_var($apiHost, FILTER_VALIDATE_URL) ? '<a href="' . $apiHost . '" target="_blank">' . $apiHost . '</a>' : "localhost";
    return '<p style="display: inline;"><a href="/admin" style="font-weight: bold; font-size: 200%; margin: 0 10px; text-decoration: none; color: inherit;">' . App::getTitle() . '</a> ' . _("Control Panel") . '. Api: ' . $apiLocation . ". " . _("Version") . ": " . App::getVersion() . '</p>';
  }

	/**
	 * @return array
	 */
  public static function mainMenu(): array
  {
		$navbar = CmsFactory::view()->fragment()->navbar()
			->newTab("/admin", CmsFactory::view()->fragment()->icon()->home())
			->newTab("/admin/config", CmsFactory::view()->fragment()->icon()->config())
			->newTab("/admin/user",_("Users"))
			->level(1);
    foreach (CmsFactory::controller()->configuration()->getModulesEnabled() as $value) {
			if (in_array($value,['Organization','Event','Person','Place','Product','Taxon'])) {
				$text = lcfirst($value);
				$navbar->newTab("/admin/$value", _($text));
			}
			if (in_array($value,['Article','Book','Certification','MediaObject','WebPage','WebPageElement','WebSite'])) {
				$navbar->newTab("/admin/creativeWork", _('Creative work'));
			}
    }
    return $navbar->ready();
  }

	/**
	 * @return string
	 */
  public static function footer(): string
  {
    return "<p>Copyright by Mauro Cruz</p>";
  }
}
