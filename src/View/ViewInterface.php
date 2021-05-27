<?php
namespace Plinct\Cms\View;

interface ViewInterface 
{
    public function index(array $data): array;

    public function new($data = null): array;

    public function edit(array $data): array;
}
