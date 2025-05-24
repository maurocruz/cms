<?php
namespace Plinct\Cms\View\WebSite\Type\Place;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class Place implements TypeViewInterface
{
  /**
   * @var int
   */
  protected int $placeId;

  /**
   *
   */
  public function navbarPlace(string $title = null): void
  {
		CmsFactory::view()->addHeader(
	    CmsFactory::View()->fragment()->navbar()
		    ->type('place')
		    ->title(_('Place'))
		    ->newTab("/admin/place", CmsFactory::view()->fragment()->icon()->home())
		    ->newTab("/admin/place/new", CmsFactory::view()->fragment()->icon()->plus())
		    ->search()
		    ->ready()
		);
    if ($title) {
	    CmsFactory::view()->addHeader(
        CmsFactory::view()->fragment()->navbar($title, [], 3)->ready()
	    );
    }
  }

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 */
  public function index(?array $data, array $queryParams = null): void
  {
    $this->navbarPlace();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('place')->ready()
		);
  }

  /**
   * @param null $value
   * @param array|null $queryParams
   */
  public function new($value = null, array $queryParams = null): void
  {
    $this->navbarPlace();
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formPlace(), _("Add new")));
  }

  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @throws Exception
   */
  public function edit(?array $data, array $queryParams = null): void
  {
		if (empty($data)) {
			CmsFactory::view()->addMain("<p>"._("Nothing found!")."</p>");
		} else {
			$value = $data[0];
			$typeBuilder = ToolBox::typeBuilder($value);
			$idplace = $typeBuilder->getId();
			$idthing = $typeBuilder->getIdthing();
			$this->placeId = isset($value) ? $idplace : null;
			// NAVBAR
			$this->navbarPlace($value['name']);
			// form
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formPlace('edit', $value), _("Edit")));
			CmsFactory::view()->addMain([
				CmsFactory::view()->fragment()->reactShell('place')->setId((string) $idplace)->ready()
			]);
			// IMAGEOBJECT
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart($idthing)->ready()
			);
		}
  }

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
  private function formPlace(string $case = 'new', array $value = null): array
  {
		$idplace = null;
		$keywords = $value['keywords'] ?? null;
		$publicAccess = $value['publicAccess'] ?? null;
    $form = CmsFactory::view()->fragment()->form("form-place", ["class" => "form-basic form-place" ]);
    $form->action("/admin/place/$case")->method("post");
		if ($case == 'edit' && !!$value) {
			$tb = CmsFactory::toolBox()::typeBuilder($value);
			$idplace = $tb->getId();
			$form->input('idplace',$idplace,'hidden');
		}
		$form->setIdform($idplace ? "form-place-edit-$idplace" : "form-place-new");
		// THING
    $form = Thing::formContent($form, $value);
		// KEYWORDS
	  $form->fieldsetWithInput('keywords',$keywords,_("Keywords"));
	  // PUBLIC ACCESS
	  $form->fieldsetWithRadio('publicAccess',[_('No'), _('Yes')],$publicAccess ? 1 : 0 , _('Public access'));
    // submit
    $form->submitButtonSend();
		if ($case == 'edit') {
			$form->submitButtonDelete('/admin/place/erase');
		}
    return $form->ready();
  }
}
