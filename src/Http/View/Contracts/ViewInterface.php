<?php
namespace Plinct\Cms\Http\View\Contracts;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Component\ComponentFactory;

interface ViewInterface
{
	public function addMain($content): void;
	public function build(array $params = null): void;
	public function component(): ComponentFactory;
	public function getQueryStrings(): array;
	public function getQuerystring(string $key): string;
	public function warning(string $message);
	public function setContext(RequestContext $context);
	public function render(): string;
}
