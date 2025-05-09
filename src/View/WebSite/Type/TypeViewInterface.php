<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type;

interface TypeViewInterface
{
	public function index(?array $data, array $queryParams = null);

	public function edit(?array $data, array $queryParams = null);

	public function new(?array $value, array $queryParams = null);
}