<?php
namespace Plinct\Cms\Middleware;

use Plinct\Cms\App;
use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\WebSite;
use Plinct\PDO\PDOConnect;;

use Plinct\Web\Debug\Debug;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GatewayMiddleware implements MiddlewareInterface {
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // IF API IS NOT SETTING
        if (!App::getApiHost()) {
            WebSite::warning(_("You need to set the API server on index.php. Insert cms->setApi(apiUrl, apiSecretKey)"));
            $request = $request->withAttribute('status','fail');
        }
        // CHECK IF CONNECTION ON
        elseif (!PDOConnect::testConnection()) {
            WebSite::warning(_("Database connection failed!"));
            $request = $request->withAttribute('status','fail');
        } else {
            // CHECK IF TABLE user EXISTS
            $tableUser = PDOConnect::run("SHOW TABLE STATUS WHERE NAME='user'");
            if (empty($tableUser)) {
                WebSite::addMain(Fragment::error()->installSqlTable('user', _("Table 'user' does not exist!")));
                $request = $request->withAttribute('status', 'fail');
            }
        }

        return $handler->handle($request);
    }
}