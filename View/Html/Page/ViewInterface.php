<?php

namespace Plinct\Cms\View\Html\Page;

interface ViewInterface 
{
    public function index(array $data): array;
    
    public function edit(array $data): array;
    
    public function new($data = null): array;
}
