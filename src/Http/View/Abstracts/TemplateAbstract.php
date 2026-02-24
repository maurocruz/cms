<?php
namespace Plinct\Cms\Http\View\Abstracts;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Domain\Auth\Userlogged;
use Plinct\Cms\Http\View\Contracts\TemplateInterface;
use Plinct\Cms\Http\View\Template\Template;

abstract class TemplateAbstract extends ComponentAbstract implements TemplateInterface
{
	private array $querystring = [];
	private RequestContext $context;

	public function __construct(protected readonly Template $template)
	{
		parent::__construct($template->getComponent());
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
			$warnc = $queryArray['wrnc'] ?? $queryArray['warnc'];
			$decoded = gzinflate(base64_decode($warnc));
			$this->warning($decoded);
		}
		// warning
		if (array_key_exists('wrn',$queryArray)) {
			$this->warning($queryArray['wrn']);
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
		$this->addMain("<p class='warning'>"._($message)."</p>");
	}
}
