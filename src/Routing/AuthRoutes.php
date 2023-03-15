<?php

declare(strict_types=1);

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;

return function (Route $route)
{
  /**
   * LOGOUT
   */
  $route->get('/logout',  function (Request $request, Response $response)
  {
    session_start();
    unset($_SESSION['userLogin']);
    setcookie("API_TOKEN", "", time() - 3600);
    return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
  });

  /**
   * POST LOGIN
   */
  $route->post('/login',  function (Request $request, Response $response)
  {
    $parseBody = $request->getParsedBody();
    $authentication = CmsFactory::request()->server()->auth()->login($parseBody['email'], $parseBody['password']);
    // AUTHORIZED
    if ($authentication['status'] == "success") {
      $token = $authentication['data']['token'];
      $tokenDecode = JWT::decode($token, App::getApiSecretKey(), ["HS256"]);

      if ($tokenDecode) {
				// cookie
        setcookie('API_TOKEN', $token, $tokenDecode->exp);
				// session
	      $_SESSION['userLogin']['name'] = $tokenDecode->name;
	      $_SESSION['userLogin']['uid'] = $tokenDecode->uid;
				// redirect
        $location = pathinfo($_SERVER['HTTP_REFERER'])['basename'] == "register" ? "/admin" : $_SERVER['HTTP_REFERER'];
        return $response->withHeader("Location", $location)->withStatus(302);

      } else {
        $authentication['status'] = 'fail';
        $authentication['message'] = "Sorry. The user exists but is not authorized. Contact administrator.";
      }
    }
    // UNAUTHORIZED
	  CmsFactory::webSite()->clearMain();
	  CmsFactory::webSite()->addMain(
			CmsFactory::response()->fragment()->auth()->login($authentication)
	  );
		// RESPONSE
	  return CmsFactory::response()->writeBody($response);

  });

  /**
   *  GROUP AUTH
   */
  $route->group('/auth', function (Route $route)
  {
    /**
     * GET LOGIN
     */
    $route->get('/login', function (Request $request, Response $response)
    {
      if (CmsFactory::request()->user()->userLogged()->getIduser()) {
        return $response->withHeader("Location", "/admin")->withStatus(302);
      }
			// RESPONSE
	    return CmsFactory::response()->writeBody($response);
    });

    if(!CmsFactory::request()->user()->userLogged()->getIduser()) {
      /**
       * REGISTER
       */
			$route->group('/register', function (Route $route)
			{
				$route->get('', function (Request $request, Response $response)
				{
					CmsFactory::webSite()->clearMain();
					CmsFactory::response()->webSite()->addMain(
						CmsFactory::response()->fragment()->auth()->register()
					);
					return CmsFactory::response()->writeBody($response);
				});

				/**
				 * REGISTER POST
				 */
				$route->post('', function (Request $request, Response $response)
				{
					$data = CmsFactory::request()->server()->auth()->register($request->getParsedBody());
					CmsFactory::webSite()->clearMain();
					if (isset($data['status']) && $data['status'] == "success") {
						CmsFactory::webSite()->addMain(
							CmsFactory::response()->fragment()->auth()->login($data)
						);
					} else {
						CmsFactory::response()->webSite()->addMain(
							CmsFactory::response()->fragment()->auth()->register($data)
						);
					}

					$response->getBody()->write(CmsFactory::response()->webSite()->ready());
					return $response;
				});
			});

      /**
       * RESET PASSWORD
       */
			$route->group('/resetPassword', function (Route $route)
			{
				/**
				 * GET
				 */
				$route->get('', function (Request $request, Response $response)
				{
					if (App::getMailHost() && App::getMailUsername() && App::getMailpassword() && App::getUrlToResetPassword()) {
						CmsFactory::response()->webSite()->addMain(CmsFactory::response()->fragment()->auth()->resetPassword());
					} else {
						CmsFactory::response()->webSite()->addMain("<p class='warning'>"._("No email server data")."</p>");
					}
					// RESPONSE
					return CmsFactory::response()->writeBody($response);
				});

				/**
				 * POST
				 */
				$route->post('', function (Request $request, Response $response)
				{
					$email = $request->getParsedBody()['email'];
					$data = CmsFactory::request()->server()->auth()->resetPassword($email);
					CmsFactory::response()->webSite()->addMain(
						CmsFactory::response()->fragment()->auth()->resetPassword($data, $email)
					);
					// RESPONSE
					return CmsFactory::response()->writeBody($response);
				});
			});

      /**
       * CHANGE PASSWORD
       */
			$route->group('/change_password', function (Route $route)
			{
				/**
				 * GET
				 */
				$route->get('', function (Request $request, Response $response)
				{
					$selector = $request->getQueryParams()['selector'] ?? null;
					$validator = $request->getQueryParams()['validator'] ?? null;

					if ($selector && $validator) {
						CmsFactory::response()->webSite()->addMain(
							CmsFactory::response()->fragment()->auth()->changePassword($request->getQueryParams())
						);
					} else {
						CmsFactory::response()->webSite()->addMain(
							CmsFactory::response()->fragment()->noContent(_("Missing data!"))
						);
					}
					// RESPONSE
					return CmsFactory::response()->writeBody($response);
				});

				$route->post('', function (Request $request, Response $response)
				{
					$params = $request->getParsedBody();
					$data = CmsFactory::request()->server()->auth()->changePassword($params);

					CmsFactory::response()->webSite()->addMain(
						CmsFactory::response()->fragment()->auth()->changePassword($params, $data)
					);
					// RESPONSE
					return CmsFactory::response()->writeBody($response);
				});
			});

    } else {
			$route->get('[/{paramsUrl:.*}]', function (Request $request, Response $response)
			{
				return $response->withHeader('Location', '/admin')->withStatus(301);
			});
    }
  });
};
