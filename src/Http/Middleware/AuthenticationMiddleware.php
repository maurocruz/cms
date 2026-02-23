<?php
namespace Plinct\Cms\Http\Middleware;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Locale;
use Plinct\Cms\Application\Authentication\AuthenticatedUser;
use Plinct\Cms\Application\Context\GetRepo;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Infrastructure\Http\ApiClient;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
	private ApiClient $apiClient;
	private AuthenticatedUser $authUser;
	private string $apiHost;
	private string $sitename;
	private string $basedir;
	private GetRepo $getRepo;

	public function __construct(ApiClient $apiClient, AuthenticatedUser $authUser, ContainerInterface $container, GetRepo $getRepo)
	{
		$this->apiClient = $apiClient;
		$this->authUser = $authUser;
		$this->getRepo = $getRepo;

		$settings = [];
		try {
			$settings = $container->get('settings');
		} catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
			error_log($e->getMessage());
		}
		if (!is_array($settings)) {
			$settings = [];
		}
		$this->apiHost = isset($settings['apiHost']) && is_string($settings['apiHost']) ? $settings['apiHost'] : '';
		$this->basedir = isset($settings['basedir']) && is_string($settings['basedir']) ? $settings['basedir'] : '';
		$this->sitename = isset($settings['sitename']) && is_string($settings['sitename']) ? $settings['sitename'] : '';
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 * @return ResponseInterface
	 * @throws Exception
	 * @throws GuzzleException
	 */
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
		$authAttr = ['name' => null, 'uid' => null];
	  $token = $_COOKIE['API_TOKEN'] ?? null;
	  $uri = $request->getUri();
		$query = $uri->getQuery();
		$modulesAvailable = $request->getAttribute('MODULES_AVAILABLE', []);
		$modulesEnabled = $request->getAttribute('MODULES_ENABLED', []);
	  $acceptLanguage = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE') ?: null;

		// CRIA O CONTEXTO
	  $context = new RequestContext();
	  $context->setBasedirectory($this->basedir);
	  $context->setHost($uri->getScheme().'://'.$uri->getHost());
	  $context->setModules($modulesAvailable, $modulesEnabled);
	  if (is_string($acceptLanguage) && $acceptLanguage !== '') {
		  $context->setLocale((new Locale())->acceptFromHttp($acceptLanguage));
	  }
	  $context->setApiHost($this->apiHost);
	  $context->setSitename($this->sitename);
		$repo = $this->getRepo->getRepo();
		$context->setVersion($repo['version'] ?? 'NAN');
		$context->setCommit($repo['commit'] ?? null);
		$context->setQuery($query);

    if (session_status() === PHP_SESSION_NONE) {
			session_start();
    }

    if ($token) {
	    $tokenParts = explode(".", $token);
	    if (count($tokenParts) == 3) {
				// ARMAZENA O TOKEN NA API CLIENT
				$this->apiClient->setUserToken($token);
				// VALIDA O TOKEN
		    if ($this->apiClient->get("auth/tokenValidator")) {
					// VERIFICA SE JÁ EXISTE UMA SESSÃO ABERTA
			    if (isset($_SESSION['userLogin'])) {
				    $name = $_SESSION['userLogin']['name'];
				    $uid = $_SESSION['userLogin']['uid'];
			    } else {
				    $payload = $tokenParts[1];
				    $json = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload));
				    $jsonArray = json_decode($json, true);
				    $name = $jsonArray['name'];
				    $uid = $jsonArray['uid'];
				    $_SESSION['userLogin']['name'] = $name;
				    $_SESSION['userLogin']['uid'] = $uid;
			    }

					$this->authUser->load($uid,$name,$token);

					// SET CONTEXT
					$context->setUser($this->authUser->getUser());

			    $authAttr['name'] = $name;
			    $authAttr['uid'] = $uid;
		    } else {
			    unset($_SESSION['userLogin']);
		    }
	    }
    }	else {
      unset($_SESSION['userLogin']);
			/*if (!in_array($route->getName(), ['loginPost','registerPost','registerGet'])) {
				throw new HttpUnauthorizedException($request);
			}*/
    }
		session_write_close();
	  // save context
	  $request = $request->withAttribute(RequestContext::class, $context);

	  $request = $request->withAttribute("auth", $authAttr);

    return $handler->handle($request);
  }
}
