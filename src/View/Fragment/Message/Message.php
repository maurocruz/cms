<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Message;

class Message
{
	public function noContent(string $message = 'No content'): string {
		return "<p class='warning'>"._($message)."</p>";
	}
	public function warning($message = 'Oops! something went wrong!'): string
	{
		$warning = '';
		if (is_array($message)) {
			if (isset($message['message'])) {
				$warning = $message['message'].". ";
			}
			$warning .= json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}
		return "<p class='warning'>".$warning ?? _($message)."</p>";
	}
}