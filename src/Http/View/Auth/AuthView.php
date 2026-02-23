<?php
namespace Plinct\Cms\Http\View\Auth;

use Plinct\Cms\Http\View\Abstracts\ViewAbstract;

class AuthView extends ViewAbstract
{
	/**
	 * @param string|null $message
	 * @return void
	 */
	public function display(string $message = null): void
	{
		if ($message) {
			$this->addMain("<p class='warning'>"._($message)."</p>");
		}
	}


	public function build(array $params = null): void
	{
		// TODO: Implement build() method.
	}
}
