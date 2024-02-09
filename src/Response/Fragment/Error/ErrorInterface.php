<?php

declare(strict_types=1);

namespace Plinct\Cms\Response\Fragment\Error;

interface ErrorInterface
{
    public function installSqlTable(string $type, string $message): array;
}