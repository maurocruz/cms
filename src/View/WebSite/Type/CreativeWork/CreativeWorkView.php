<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class CreativeWorkView extends CreativeWorkViewAbstract implements TypeViewInterface
{
	/**
	 *
	 */
	public function __construct()
	{
		parent::navbar();
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('creativeWork')->setColumnsTable(['@type'=>_('Types')])->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
		if (isset($data[0])) {
			$value = $data[0];
			$typeBuilder = new TypeBuilder('creativeWork', $value);
			$this->idcreativeWork = $typeBuilder->getId();
			$idthing = $typeBuilder->getPropertyValue('idthing');
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Creative work"), parent::form('edit', $value), true));
			// images
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart((int)$idthing)->ready()
			);
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("No creative work were found!")));
		}
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function new(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(parent::form())
		);
	}
}