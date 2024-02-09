<?php
declare(strict_types=1);
namespace Plinct\Cms\logger;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as Monolog;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\ProcessorInterface;
use Plinct\Cms\App;

class Logger {
	private string $channel;
	private string $filename;
	private Level $level = Level::Debug;
	private ?ProcessIdProcessor $processor = null;

	public function __construct(string $channel, string $filename = 'plinctCms.log')
	{
		$this->channel = $channel;
		$this->filename = $filename;
		return $this;
	}

	public function info(string $message, array $context = [], ProcessorInterface $processor = null)
	{
		$this->level = Level::Info;
		$this->processor = $processor;
		$this->write($message, $context);
	}

	public function warning(string $message, array $context = [], ProcessorInterface $processor = null)
	{
		$this->level = Level::Warning;
		$this->processor = $processor;
		$this->write($message, $context);
	}

	public function error(string $message, array $context = [], ProcessorInterface $processor = null)
	{
		$this->level = Level::Error;
		$this->processor = $processor;
		$this->write($message, $context);
	}

	public function critical(string $message, array $context = [], ProcessorInterface $processor = null)
	{
		$this->level = Level::Critical;
		$this->processor = $processor;
		$this->write($message, $context);
	}

	private function write(string $message, array $context = [])
	{
		$monolog = new Monolog($this->channel);
		$monolog->pushHandler(new StreamHandler(App::getLogdir().$this->filename, $this->level));
		if ($this->processor) {
			$monolog->pushProcessor($this->processor);
		}
		switch ($this->level) {
			case Level::Info: $monolog->info($message, $context); break;
			case Level::Warning: $monolog->warning($message, $context); break;
			case Level::Error: $monolog->error($message, $context); break;
			case Level::Critical: $monolog->critical($message, $context); break;
		}
	}
}
