<?php

namespace Plinct\Cms\View;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Plinct\Cms\View\Html\HtmlView;

class ViewBuilder
{    
    protected $view;

    public function __construct()
    {
        $this->view = new HtmlView();        
    }
    
    public function build(Request $request, Response $response) 
    {        
        if($this->view) {
            $view = $this->view->build($request, $response);
            $response->getBody()->write($view);
        }
        
        return $response;        
    }
    
    public function login(Request $request, Response $response) 
    {
        if ($this->view) {
            $view = $this->view->login($request, $response);
            $response->getBody()->write($view);
        } else {
            $data = (new \fwc\Thing\SoftwareApplicationGet())->selectById(0);
            $response = $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            $response->getBody()->write($data);
        }
        
        return $response;        
    }
    
    public function register() 
    {        
        return $this->view->register();
    }
    
    public function ready() 
    {
        return $this->view->ready();
    }
}
