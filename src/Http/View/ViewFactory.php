<?php
namespace Plinct\Cms\Http\View;

use Plinct\Cms\Http\View\Auth\AuthView;
use Plinct\Cms\Http\View\Template\Template;
use Psr\Http\Message\ResponseInterface;

class ViewFactory
{

	/**
	 * @param ResponseInterface $response
	 * @param Template $template
	 * @return AuthView
	 */
	public function authView(ResponseInterface $response, Template $template): AuthView
	{
		return new AuthView($template);
	}
}
