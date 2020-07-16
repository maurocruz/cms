<?php

namespace Plinct\Cms\Controller;

interface ControllerInterface 
{
    public function index(): array;
    
    public function edit(array $params): array;
}
