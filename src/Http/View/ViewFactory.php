<?php
namespace Plinct\Cms\Http\View;

use Plinct\Cms\Http\View\Template\Template;
use Psr\Http\Message\ResponseInterface;

class ViewFactory
{
	public function dashboardView(ResponseInterface $response, Template $template): DashboardView
	{
		return new DashboardView($response, $template);
	}
}
