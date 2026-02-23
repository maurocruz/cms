<?php
namespace Plinct\Cms\Infrastructure\Cache;

use Plinct\Cms\Domain\Cache\CacheInterface;

class FileCache implements CacheInterface
{
	private string $path;

	public function __construct(string $path) {
		$this->path = $path;
	}

	private function file(string $key): string
	{
		return $this->path . '/' . $key . '.cache';
	}


	public function setKey(string $namespace, string $name, array $context): string
	{
		$payload = $namespace . '|' . $name . '|' . json_encode($context, JSON_UNESCAPED_UNICODE);
		return $namespace . '_' . $name . '_' . sha1((string)$payload);
	}


	public function get(string $key, mixed $default = null): mixed
	{
		$file = $this->file($key);

		if (!file_exists($file)) {
			return $default;
		}

		$data = unserialize(file_get_contents($file));

		if ($data['expires_at'] < time()) {
			unlink($file);
			return $default;
		}

		return $data['value'];
	}

	public function set(string $key, mixed $value, int $ttl = 3600): void
	{
		$file = $this->file($key);

		$data = [
			'expires_at' => time() + $ttl,
			'value' => $value,
		];

		file_put_contents($file, serialize($data));
	}

	public function delete(string $key): void
	{
		$file = $this->file($key);

		if (file_exists($file)) {
			unlink($file);
		}
	}

	public function clear(): void
	{
		foreach (glob($this->path . '/*.cache') as $file) {
			unlink($file);
		}
	}

	public function has(string $key): bool
	{
		return $this->get($key) !== null;
	}
}
