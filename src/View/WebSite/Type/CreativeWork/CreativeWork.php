<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class CreativeWork extends CreativeWorkAbstract implements TypeInterface
{

	public function __construct()
	{
		parent::navbar();
	}

	/**
	 * @param array|null $value
	 * @return void
	 */
	public function index(?array $value): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('creativeWork')->setColumnsTable(['type'=>_('Types')])->ready()
		);
	}

	public function edit(?array $data)
	{
		if (isset($data[0])) {
			$value = $data[0];
			$typeBuilder = new TypeBuilder('creativeWork', $value);
			$this->idcreativeWork = $typeBuilder->getId();
			$idthing = $typeBuilder->getPropertyValue('idthing');
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Creative work"), parent::form('edit', $value), true));
			// images
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->reactShell('imageObject')->setIsPartOf($idthing)->ready()
			);
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("No creative work were found!")));
		}
	}

	public function new(?array $value)
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(parent::form())
		);
	}
}