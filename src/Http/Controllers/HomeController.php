<?php
namespace Plinct\Cms\Http\Controllers;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Template\Template;
use Plinct\Cms\Http\View\ViewFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController
{
	private ViewFactory $view;
	private Template $template;

	public function __construct(ViewFactory $view, Template $template)
	{
		$this->view = $view;
		$this->template = $template;
	}

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function home(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$user = $context->getUser();
		$this->template->setContext($context);

		if ($user) {
			$module = $request->getAttribute('module');
			if ($module == 'login') {
				return $response->withHeader("Location", "/admin")->withStatus(302);
			} else {
				if ($module && !$user->hasPrivilege(1,'r',$module)) {
					$response->getBody()->write("Sem privilégio");
				} else {
					$dashboard = $this->view->dashboardView($response, $this->template);
					$dashboard->render();
				}
			}
		} else {
			$response->getBody()->write("Não logou!");
		}
		return $response;
	}
}
