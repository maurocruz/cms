<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Place;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class Place implements TypeInterface
{
  /**
   * @var int
   */
  protected int $placeId;

  /**
   *
   */
  public function navbarPlace(string $title = null)
  {
		CmsFactory::view()->addHeader(
	    CmsFactory::View()->fragment()->navbar(_("Place"), [
	        "/admin/place" => CmsFactory::view()->fragment()->icon()->home(18,18),
	        "/admin/place/new" => CmsFactory::view()->fragment()->icon()->plus(18,18)
	    ], 2, ['table'=>'place'])->ready()
		);
    if ($title) {
	    CmsFactory::view()->addHeader(
        CmsFactory::view()->fragment()->navbar($title, [], 3)->ready()
	    );
    }
  }

	/**
	 * @param array|null $value
	 */
  public function index(?array $value)
  {
    $this->navbarPlace();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('place')->ready()
		);
  }

  /**
   * @param null $value
   */
  public function new($value = null)
  {
    $this->navbarPlace();
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formPlace(), _("Add new")));
  }

  /**
   * @param ?array $data
   * @throws Exception
   */
  public function edit(?array $data): void
  {
		if (empty($data)) {
			CmsFactory::view()->addMain("<p>"._("Nothing found!")."</p>");
		} else {
			$value = $data[0];
			$typeBuilder = new TypeBuilder('place',$value);
			$idplace = $typeBuilder->getId();
			$this->placeId = isset($value) ? $idplace : null;
			// NAVBAR
			$this->navbarPlace($value['name']);
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('place')->setIsPartOf($idplace)->ready());
		}
  }

	/**
	 * @return array
	 */
  private function formPlace(): array
  {
    $form = CmsFactory::view()->fragment()->form([ "id" => "form-place-new", "name" => "place-form-new", "class" => "form-basic form-place" ]);
    $form->action("/admin/place/new")->method("post");
    $form = Thing::formContent($form);
    // submit
    $form->submitButtonSend();
    return $form->ready();
  }
}
