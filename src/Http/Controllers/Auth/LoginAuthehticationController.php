<?php
namespace Plinct\Cms\Http\Controllers\Auth;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Authentication\LoginUser;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Auth\LoginView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class LoginAuthehticationController
{
	public function __construct(private LoginUser $loginUser, private LoginView $view)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$params = $request->getParsedBody();
		unset($params['submit']);
		$loginResponse = $this->loginUser->login($params['email'], $params['password']);
		// IF FAIL
		if (isset($loginResponse['status']) && $loginResponse['status'] === "fail")	{
			// REVIEW FORM LOGIN WITH MESSAGE
			$this->view->setContext($context);
			$this->view->build();
			$response->getBody()->write($this->view->render());
		} elseif ($loginResponse) {
			return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
		} else {
			$response->getBody()->write("Login Failed");
		}
		return $response;
	}

}
