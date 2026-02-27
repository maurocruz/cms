<?php
namespace Plinct\Cms\Http\View\Abstracts;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Domain\Auth\Userlogged;
use Plinct\Cms\Http\Support\SupportHttp;
use Plinct\Cms\Http\View\Contracts\TemplateInterface;
use Plinct\Cms\Http\View\Template\Template;

abstract class TemplateViewAbstract implements TemplateInterface
{
	private array $querystring = [];
	private RequestContext $context;

	public function __construct(protected readonly Template $template)
	{
	}

	/**
	 * @param RequestContext $context
	 * @return void
	 */
	public function setContext(RequestContext $context): void
	{
		$this->context = $context;
		$this->template->setContext($context);
		// PARSE QUERYSTRINGS
		parse_str($context->getQuery(),$queryArray);
		$this->querystring = $queryArray;
		// warning cript
		if (array_key_exists('warnc',$queryArray) || array_key_exists('wrnc',$queryArray)) {
			$message = $queryArray['wrnc'] ?? $queryArray['warnc'];
			$this->warning(SupportHttp::decodeCript($message));
		}
		// warning
		if (array_key_exists('wrn',$queryArray)) {
			$this->warning($queryArray['wrn']);
		}
		// notice
		if (array_key_exists('ntc',$queryArray)) {
			$this->notice($queryArray['ntc']);
		}
	}

	public function getContext(): RequestContext
	{
		return $this->context;
	}


	/**
	 * @param string $key
	 * @return string
	 */
	public function getQuerystring(string $key): string
	{
		return $this->querystring[$key] ?? '';
	}

	/**
	 * @return array
	 */
	public function getQueryStrings(): array
	{
		return $this->querystring;
	}

	public function getUser(): Userlogged
	{
		return $this->context->getUser();
	}
	/**
	 * @param $content
	 * @return void
	 */
	public function addMain($content): void
	{
		$this->template->addMain($content);
	}


	/**
	 */
	public function render(): string
	{
		return $this->template->render();
	}

	public function warning(string $message): void
	{
		$this->addMain("<div class='warning'><p>"._($message)."</p></div>");
	}

	public function notice(string $message): void
	{
		$this->addMain("<div class='notice'><p>"._($message)."</p></div>");
	}
}
