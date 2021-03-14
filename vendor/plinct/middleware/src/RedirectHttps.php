<?php

namespace Plinct\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class RedirectHttps
{
    public function __invoke(Request $request, RequestHandler $handle) 
    {   
        $response = $handle->handle($request);
        
        if (substr($_SERVER['HTTP_HOST'], 0, 4) === 'www.' || $_SERVER['SERVER_PORT'] != 443 && (self::checkForwaredPort(445) === false && self::checkForwaredProto("https") === false)) 
        {
            $host = substr($_SERVER['HTTP_HOST'], 0, 4) === 'www.' ? substr($_SERVER['HTTP_HOST'], 4) : $_SERVER['HTTP_HOST'];
            
            return $response->withHeader('Location', 'https://' . $host . $_SERVER['REQUEST_URI'])->withStatus(302);            
            
        } else {
            return $response;
        }
    }
    
    private function checkForwaredPort(string $value)
    {
        if (array_key_exists('HTTP_X_FORWARDED_PORT', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_PORT'] == $value){
            return true;
            
        } else {
            return false;
        }
    }
    
    private function checkForwaredProto(string $value)
    {
        if (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == $value){
            return true;
            
        } else {
            return false;
        }
    }
}
