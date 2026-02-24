<?php
namespace Plinct\Cms\Http\View\Contracts;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Domain\Auth\Userlogged;

interface TemplateInterface
{
	public function addMain($content): void;
	public function build(array $data = null): void;
	public function getQueryStrings(): array;
	public function getQuerystring(string $key): string;
	public function getUser(): ?Userlogged;
	public function warning(string $message);
	public function setContext(RequestContext $context);
	public function render(): string;
}
