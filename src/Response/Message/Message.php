<?php

declare(strict_types=1);

namespace Plinct\Cms\Response\Message;

class Message
{
	public function noContent(string $message = 'No content'): string {
		return "<p class='warning'>"._($message)."</p>";
	}
	public function warning(string $message = 'Oops! something went wrong!'): string {
		return "<p class='warning'>"._($message)."</p>";
	}
}