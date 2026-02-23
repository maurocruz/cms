<?php
namespace Plinct\Cms\Http\View\Template;

use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Tool\Locale;
use Plinct\Web\Render;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Template extends TemplateAbstract
{
	private ComponentFactory $component;
	private RequestContext $context;

	/**
	 */
	public function __construct(ContainerInterface $container, ComponentFactory $component)
	{
		$settings = '';
		try {
			$settings = $container->get('settings');
		} catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
			error_log($e->getMessage());
		}

		$basedirectory = $settings['basedirectory'] ?? __DIR__ . "/../../../../";
		// LANGUAGE
		$locale = Locale::getServerLanguage();
		$this->HTML['attributes'] = ["lang" => $locale];
		// TRANSLATE BY GETTEXT
		Locale::translateByGettext($locale, "plinctCms", $basedirectory."/Locale");

		$this->component = $component;
	}

	/**
	 * @return ComponentFactory
	 */
	public function getComponent(): ComponentFactory
	{
		return $this->component;
	}

	/**
	 * @param RequestContext $context
	 */
	public function setContext(RequestContext $context): void
	{
		$this->context = $context;
	}

	/**
	 */
	public function render(): string
	{
		// HEAD
		$head = new Head($this->context);
		$this->addHead($head->get());

		// HEADER
		$header = new Header($this->component, $this->context);
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
		return "<!DOCTYPE html>" . Render::arrayToString(parent::getHTML());
	}
}
