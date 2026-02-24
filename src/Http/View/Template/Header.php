<?php
namespace Plinct\Cms\Http\View\Template;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Domain\Auth\Userlogged;
use Plinct\Cms\Http\View\Component\ComponentFactory;

class Header
{
	private ?Userlogged $user;
	private string $version;
	private string $commit;
	private ComponentFactory $componentFactory;
	private RequestContext $context;

	/**
	 */
	public function __construct(ComponentFactory $componentFactory, RequestContext $context)
	{
		$this->user = $context->getUser();
		$this->version = $context->getVersion();
		$this->commit = $context->getCommit();
		$this->componentFactory = $componentFactory;
		$this->context = $context;
	}

	/**
	 * @return string
	 */
	public function userBar(): string
	{
		if (!$this->user) return '';
		$helloText = sprintf(_("Hello, %s."), $this->user->getUsername());
		return "<div class='admin admin-bar-top'>
    <p>$helloText</p>
    <button class='button-link' onclick='navigator.clipboard.writeText(\"" . $this->user->getToken() . "\")'>Copy token</button>
    <p><a href='/admin/auth/logout'>" . _("Log out") . "</a></p>
    </div>";
	}

	/**
	 * @return string
	 */
	public function header(): string
	{
		$apiHost = $this->context->getApiHost();
		$apiLocation = $apiHost && filter_var($apiHost, FILTER_VALIDATE_URL) ? '<a href="' . $apiHost . '" target="_blank">' . $apiHost . '</a>' : "localhost";
		$version = $this->commit ?
			_('Working in localhost').". "._('Version').": <b>$this->version</b>; Commit: <b>$this->commit</b>"
			:  _('Version').": <b>$this->version</b>.";

		return '<p style="display: inline;"><a href="/admin" style="font-weight: bold; font-size: 200%; margin: 0 10px; text-decoration: none; color: inherit;">' . $this->context->getSitename() . '</a> ' . _("Control Panel") . '. Api: ' . $apiLocation .". ". $version . '</p>';
	}

	/**
	 * @return ?array
	 */
	public function mainMenu(): ?array
	{
		if (!$this->user) return null;

		$navbar = $this->componentFactory->navbar()
			->newTab("/admin", $this->componentFactory->icon()->home())
			->newTab("/admin/config", $this->componentFactory->icon()->config())
			->newTab("/admin/user",_("Users"))
			->level(1);
		$tabs = [];
		foreach ($this->context->getModulesEnabled() as $value) {
			if (in_array($value,['Action','Organization','Event','Person','Place','Product','Taxon'])) {
				$url = lcfirst($value);
				$tabs["/admin/$url"] =  ucfirst($value);
			}
			if (in_array($value,['Article','Book','Certification','MediaObject','WebPage','WebPageElement','WebSite'])) {
				$tabs["/admin/creativeWork"] =  "Creative work";
			}
		}
		foreach ($tabs as $key => $tab) {
			$navbar->newTab($key, _($tab));
		}
		return $navbar->ready();
	}
}
