<?php

namespace Plinct\Cms\Controller;

interface ControllerInterface 
{
    public function index($params = null): array;
    
    public function edit(array $params): array;
    
    public function new($params = null);
}
