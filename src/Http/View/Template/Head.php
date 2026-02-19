<?php
namespace Plinct\Cms\Http\View\Template;

use Plinct\Cms\Application\Context\RequestContext;

class Head
{
	private string $host;
	private string $title;
	private string $basedir;

	/**
	 */
	public function __construct(RequestContext $context)
	{
		$this->host = $context->getHost();
		$this->title = $context->getSitename();
		$this->basedir = $context->getBasedirectory();
	}

	public function get(): string
	{
		$cssReset = file_get_contents($this->basedir . '/static/css/reset.css');
		$cssEstilos = file_get_contents($this->basedir . '/static/css/estilos.css');
		$cssStyle = file_get_contents($this->basedir . '/static/css/style.css');
		$cssStyleDark = file_get_contents($this->basedir . '/static/css/style-dark.css');
		$jsScripts = file_get_contents($this->basedir . '/static/js/scripts.js');


		$returns = '<meta charset="UTF-8">';
		$returns .= '<meta name="viewport" content="width=device-width">';
		$returns .= "<link rel='shortcut icon' href='$this->host/favicon.ico' type='image/x-icon'>";
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
        config.toolbar_mytoolbar = "{bold,italic,underline,strike,superscript,subscript}|{fontsize,paragraphs}|{forecolor,backcolor}|{justifyleft,justifycenter,justifyright,justifyfull}|{insertorderedlist,insertunorderedlist}|{insertchars}|{insertlink,unlink,insertimage}|removeformat|insertcode"
        +"#{undo,redo,fullscreenenter,fullscreenexit,code}";
			</script> ';
		$returns .= '<script src="https://plinct.com.br/static/dist/plinct-shell/3.2/main.js"></script>';
		$returns .= '<title>Plinct CMS [' . $this->title . ']</title>';
		$returns .= '<meta name="description" content="CMS for Plinct">';
		return $returns;
	}
}
