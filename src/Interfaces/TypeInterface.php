<?php

declare(strict_types=1);

namespace Plinct\Cms\Interfaces;

interface TypeInterface
{
	public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array;
}