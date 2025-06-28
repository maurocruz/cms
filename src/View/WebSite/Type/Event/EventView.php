<?php
namespace Plinct\Cms\View\WebSite\Type\Event;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class EventView extends EventAbstract implements TypeViewInterface
{

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 */
  public function index(?array $data, array $queryParams = null): void
  {
    // NAVBAR
    $this->navbarEvent();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('Event')->setColumnsTable(["startDate"=>"Início"])->ready()
		);
  }

  /**
   *
   * @param array|null $data
   * @param array|null $queryParams
   */
  public function new(?array $data, array $queryParams = null): void
  {
    // NAVBAR
    $this->navbarEvent();
    // FORM
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(parent::formEvent(), _("Add new")));
  }

  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @throws Exception
   */
  public function edit(?array $data, array $queryParams = null): void
  {
    // NAVBAR
    $this->navbarEvent();
    if (!$data) {
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->miscellaneous()->message(_("Event not found")));
    } else {
      $value = $data[0];
			$typeBuilder = CmsFactory::toolBox()::typeBuilder($value);
      $this->idevent = $typeBuilder->getId();
			$idthing = $typeBuilder->getIdthing();
      // EVENT FORM
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formEvent('edit', $value), _("Edit event")));
			// ADDITIONAL TYPES
	    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('event')->setProperty('additionalType')->setIdHasPart($idthing)->ready());
			// SUB EVENTS
      CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->expandingBox(
					_("Sub Events"),
					CmsFactory::view()->fragment()->form("form-event")->relationshipOneToMany("event", $idthing, 'event', $value['subEvent'] ?? null, "startDate")
				)
      );
      // IMAGE
	    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart($idthing)->ready());
    }
  }
}
