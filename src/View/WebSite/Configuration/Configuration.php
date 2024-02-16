<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Configuration;

use Plinct\Cms\CmsFactory;

class Configuration
{
	public function index(array $data = null)
	{
		CmsFactory::view()->addMain("<h1>"._("Configuration")."</h1>");
	}

	/**
	 * @param string $type
	 * @return null
	 */
	public function installSqlTable(string $type)
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
}
