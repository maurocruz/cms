<?php
namespace Plinct\Cms\Http\View\Config;

use Plinct\Cms\Http\View\Abstracts\ModuleViewAbstract;
use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Cms\Http\View\Template\Template;

class ConfigView extends ModuleViewAbstract
{
	public function __construct(ComponentFactory $componentFactory, Template $template)
	{
		parent::__construct($componentFactory, $template);
	}

	public function build(array $data = null): void
	{
		$this->addNavbar(_('Configuration'),2,[
			'/admin/configuration' => $this->icon()->home(),
			'/admin/thing' => _('Thing')
		]);
		// MODULES ENABLED
		$modulesEnabled = $this->getContext()->getModulesEnabled();
		$this->addMain([
			"<h1>"._("Configuration")."</h1>",
			"<dl>",
			"<dt>"._('Modules Enabled')."</dt>",
			"<dd>"
		]);
		if (empty($modulesEnabled)) {
			$this->addMain(_('No Modules enabled'));
		} else {
			$this->addMain("<ul>");
			foreach ($modulesEnabled as $item) {
				$this->addMain("<li>" . _($item) . "</li>");
			}
			$this->addMain("</ul>");
		}
		$this->addMain("</dd></dl>");

		// MODULES AVAILABLE
		$modulesAvailable = $this->getContext()->getModulesAvailable();

		$this->addMain("<table>");
		$this->addMain("<caption>"._('Modules Available')."</caption>");
		$this->addMain("<thead><tr><th>Module</th><th>"._('Installation status')."</th></tr></thead>");
		$this->addMain("<tbody>");
		foreach ($modulesAvailable as $item) {
			$isInstalled = in_array($item, $modulesEnabled);
			$this->addMain("<tr></tr><td>"._($item)."</td>");
			$this->addMain("<td>");
			$this->addMain("<form action='/admin/config/installModule' method='post' class='form-config-installModule'>");
			$this->addMain("<input type='hidden' name='moduleName' value='$item'/>");
			$this->addMain(!$isInstalled ? " <button class='button'>"._('Install module')."</button>" : _('Module installed'));
			$this->addMain("</form>");
			$this->addMain("</td></tr>");
		}
		$this->addMain("</tbody>");
		$this->addMain("</table>");
	}
}

