<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\WebSite;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class WebSite extends WebSiteAbstract implements TypeInterface
{
	/**
	 * @param array|null $value
	 */
  public function index(?array $value) {
    $this->navbarWebSite();
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webSite')->setColumnsTable(['url'=>'Url'])->ready());
  }

  /**
   * @param array|null $value
   */
  public function new(?array $value) {
    // NAVBAR
    $this->navbarWebSite();
    // FORM
    CmsFactory::view()->addMain(self::newView());
  }

  /**
   * @param ?array $data
   */
  public function edit(?array $data) {
    $value = $data[0] ?? null;
		$typeBuilder = new TypeBuilder('webSite', $value);
		$value['idwebSite'] = $typeBuilder->getId();
		if ($value) {
			$this->setIdwebSite($value['idwebSite']);
			// navbar
			parent::navbarWebSite($value['name']);
			// form
			CmsFactory::view()->addMain(parent::editView($value));
		} else {
			parent::navbarWebSite();
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent('Nothing found!'));
		}
  }
}
