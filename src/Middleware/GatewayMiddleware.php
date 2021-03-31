<?php
namespace Plinct\Cms\Middleware;

use Plinct\Cms\App;
use Plinct\Cms\View\Template\TemplateController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class GatewayMiddleware implements MiddlewareInterface {
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $response = $handler->handle($request);
        $warning = null;
        // IF API IS NOT SETTING
        if (!App::getApiHost()) {
            $warning[] = _("You need to set the API server!");
        }
        if ($warning) {
            $template = new TemplateController();
            foreach ($warning as $value) {
                $template->warning($value);
            }
            $response = new Response();
            $response->getBody()->write($template->ready());
        }
        return $response;
    }
}