<?php
namespace Plinct\Cms\Http\View;

use Plinct\Cms\Http\View\Template\Template;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class DashboardView
{
	protected ResponseInterface $response;
	protected Template $template;

	public function __construct(ResponseInterface $response, Template $template)
	{
		$this->response = $response;
		$this->template = $template;
	}

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function render(): void
	{
		$this->template->addMain('<h1>Dashboard</h1>');
		$this->template->render($this->response);
	}
}
