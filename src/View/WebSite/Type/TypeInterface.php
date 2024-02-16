<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type;

interface TypeInterface
{
	public function index(?array $value);

	public function edit(?array $value);

	public function new(?array $value);
}