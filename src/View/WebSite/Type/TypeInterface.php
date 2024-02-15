<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type;

interface TypeInterface
{
	public function index(?array $data);

	public function edit(?array $data);

	public function new(?array $data);

	public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array;
}