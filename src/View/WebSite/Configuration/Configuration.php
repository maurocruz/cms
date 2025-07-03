<?php
namespace Plinct\Cms\View\WebSite\Configuration;

use Plinct\Cms\CmsFactory;

class Configuration
{
	/**
	 * @return void
	 */
	protected function navbar(): void
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_('Configuration'))
				->newTab('/admin/configuration', CmsFactory::view()->fragment()->icon()->home())
				->newTab('/admin/thing', _('Thing'))
				->ready()
		);
	}

	/**
	 * @return void
	 */
	public function index(): void
	{
		$modulesAvailable = CmsFactory::controller()->configuration()->getModulesAvailable();
		$modulesEnabled = CmsFactory::controller()->configuration()->getModulesEnabled();
		// NAVBAR
		self::navbar();
		// write
		CmsFactory::view()->addMain("<h1>"._("Configuration")."</h1>");
		CmsFactory::view()->addMain("<dl>");

		// MODULES ENABLED
		CmsFactory::view()->addMain("<dt>"._('Modules Enabled')."</dt>");
		CmsFactory::view()->addMain("<dd>");
		if (empty($modulesEnabled)) {
			CmsFactory::view()->addMain(_('No Modules enabled'));
		} else {
			CmsFactory::view()->addMain("<ul>");
			foreach ($modulesEnabled as $item) {
				CmsFactory::view()->addMain("<li>" . _($item) . "</li>");
			}
			CmsFactory::view()->addMain("</ul>");
		}
		CmsFactory::view()->addMain("</dd>");

		CmsFactory::view()->addMain("</dl>");

		// MODULES AVAILABLE
		CmsFactory::view()->addMain("<table>");
		CmsFactory::view()->addMain("<caption>"._('Modules Available')."</caption>");
		CmsFactory::view()->addMain("<thead><tr><th>Module</th><th>Is instaled?</th></tr></thead>");
		CmsFactory::view()->addMain("<tbody>");
		foreach ($modulesAvailable as $item) {
			$isInstalled = in_array($item, $modulesEnabled);
			CmsFactory::view()->addMain("<tr></tr><td>"._($item)."</td>");
			CmsFactory::view()->addMain("<td>");
			CmsFactory::view()->addMain("<form action='/admin/config/installModule' method='post' class='form-config-installModule'>");
			CmsFactory::view()->addMain("<input type='hidden' name='module' value='$item'/>");
			CmsFactory::view()->addMain(!$isInstalled ? " <button class='button'>"._('Install module')."</button>" : _('Module was installed!'));
			CmsFactory::view()->addMain("</form>");
			CmsFactory::view()->addMain("</td></tr>");
		}
		CmsFactory::view()->addMain("</tbody>");
		CmsFactory::view()->addMain("</table>");

	}

	/**
	 * @param string $type
	 * @return null
	 */
	public function installSqlTable(string $type): null
	{
		return CmsFactory::view()->addMain("
<div class='warning'>
	<p>".sprintf(_('The module %s does not exist!'), $type)."</p>
	<form action='/admin/config/installModule' method='post'>
		<input type='hidden' name='module' value='$type'/>
		<input type='submit' value='"._("Do you want to install it?")."' style='padding: 3px 8px;' >	
	</form>
</div>");
	}

	public function sitemap()
	{
	}
}
