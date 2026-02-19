<?php
namespace Plinct\Cms\Http\View\Template;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Fragment\FragmentFactory;
use Plinct\Tool\Locale;
use Plinct\Web\Render;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Template extends TemplateAbstract
{
	private FragmentFactory $fragment;
	private RequestContext $context;

	/**
	 */
	public function __construct(FragmentFactory $fragment)
	{
		$this->fragment = $fragment;
	}

	/**
	 * @param RequestContext $context
	 */
	public function setContext(RequestContext $context): void
	{
		$this->context = $context;
	}

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function render($response): void
	{
		// LANGUAGE
		$locale = $this->context->getLocale();
		$basedirectory = $this->context->getBasedirectory();
		$this->HTML['attributes'] = ["lang" => $locale];

		// TRANSLATE BY GETTEXT
		Locale::translateByGettext($locale, "plinctCms", $basedirectory."/Locale");

		// HEAD
		$head = new Head($this->context);
		$this->addHead($head->get());

		// HEADER
		$header = new Header($this->fragment, $this->context);
		$this->addHeader([
			$header->userBar(),
			$header->header(),
			$header->mainMenu()
		]);

		// BODY
		$this->addHTML([
			$this->getHEAD(),
			$this->getBODY()
		]);
		// WRITE
		$response->getBody()->write("<!DOCTYPE html>" . Render::arrayToString(parent::getHTML()));
	}
}
