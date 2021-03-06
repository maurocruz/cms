<?php
namespace Plinct\Cms\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Plinct\Api\Server\PDOConnect;

class InitialChecking 
{    
    public function __invoke(Request $request, RequestHandler $handle): ResponseInterface
    {
        // check if database
        $pdo = PDOConnect::getPDOConnect();
        $result = $pdo->query("SHOW TABLES");
        if($result->rowCount() == 0) {
            $request = $request->withAttribute("tablesNotExists", true);
        }
        return $handle->handle($request);
    }
}
