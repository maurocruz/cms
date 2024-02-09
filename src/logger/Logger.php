<?php
declare(strict_types=1);
namespace Plinct\Cms\logger;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as Monolog;
use Monolog\Processor\ProcessorInterface;
use Plinct\Cms\App;

class Logger
{
	private Monolog $monolog;

	public function __construct(string $channel, string $filename = 'plinctCms.log')
	{
		$this->monolog = new Monolog($channel);
		$this->monolog->pushHandler(new StreamHandler(App::getLogdir().$filename, Level::Debug));
		return $this;
	}

	public function info(string $message, array $context = [], ProcessorInterface $processor = null)
	{
		$this->processor($processor);
		$this->monolog->info($message, $context);
	}

	public function error(string $message, array $context = [], ProcessorInterface $processor = null)
	{
		$this->processor($processor);
		$this->monolog->error($message, $context);
	}

	private function processor(ProcessorInterface $processor = null) {
		if ($processor) {
			$this->monolog->pushProcessor($processor);
		}
	}
}
