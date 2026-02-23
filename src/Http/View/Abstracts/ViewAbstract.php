<?php
namespace Plinct\Cms\Http\View\Abstracts;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Cms\Http\View\Contracts\ViewInterface;
use Plinct\Cms\Http\View\Template\Template;

abstract class ViewAbstract implements ViewInterface
{

	private array $querystring = [];

	public function __construct(private readonly Template $template)
	{
	}

	/**
	 * @return ComponentFactory
	 */
	public function component(): ComponentFactory
	{
		return $this->template->getComponent();
	}

	/**
	 * @param RequestContext $context
	 * @return void
	 */
	public function setContext(RequestContext $context): void
	{
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
