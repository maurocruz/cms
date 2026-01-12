<?php
namespace Plinct\Cms\View\Fragment\Button;

use Plinct\Cms\CmsFactory;

class Buttons
{
	/**
	 * @param string $type
	 * @param string $idtype
	 * @param array|null $attributes
	 * @return array
	 */
  public function buttonDelete(string $type, string $idtype, array $attributes = null): array
  {
    $form = CmsFactory::view()->fragment()->form("form-buttonDelete",$attributes);
    $form->action("/admin/$type/erase")->method('post');
    $form->input("id$type", $idtype, 'hidden');
    $form->content("<button type='submit' class='button-submit form-submit-button-delete' onclick='return confirm(\"" . _("Do you really want to delete this item?") . "\")'>"
        . CmsFactory::view()->fragment()->icon()->delete()
      ."</button>");
    return $form->ready();
  }
}
