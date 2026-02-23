<?php
namespace Plinct\Cms\Domain\Cache;

interface CacheInterface
{
	public function setKey(string $namespace, string $name, array $context): string;

	public function get(string $key, mixed $default = null): mixed;

	public function set(string $key, mixed $value, int $ttl = 3600): void;

	public function delete(string $key): void;

	public function clear(): void;

	public function has(string $key): bool;
}
