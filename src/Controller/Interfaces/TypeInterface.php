<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Interfaces;

interface TypeInterface
{
	public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array;
}