<?php
namespace Plinct\Cms\Http\View\Contracts;

interface ModulesViewInterface
{
	public function index(array $data = null): void;
	public function new(array $data = null): void;
	public function edit(array $data = null): void;
}
