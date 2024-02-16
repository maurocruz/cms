<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Intangible;

use Plinct\Cms\CmsFactory;

class PropertyValueView
{
	/**
	 * @param string $tableHasPart
	 * @param string $idHasPart
	 * @param array $data
	 * @return array
	 */
  public function getForm(string $tableHasPart, string $idHasPart, array $data): array {
    foreach ($data as $value) {
      if (isset($value['identifier'])) {
        $content[] = self::formPropertyValue($tableHasPart, $idHasPart, 'edit', $value);
      }
    }
    // new
    $content[] = self::formPropertyValue($tableHasPart, $idHasPart);
    return $content;
  }

	/**
	 * @param string $tableHasPart
	 * @param string $idHasPart
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
  protected function formPropertyValue(string $tableHasPart, string $idHasPart, string $case = "new", array $value = null): array
  {
	  $form = CmsFactory::view()->fragment()->form(["id" => "form-attributes-$case-$tableHasPart-$idHasPart", "name" => "form-attributes--$case", "class" => "formPadrao form-propertyValue"])->action("/admin/PropertyValue/$case")->method('post');
	  // HIDDENS
	  $form->input('tableHasPart', $tableHasPart, 'hidden');
	  // NEW
	  if ($case == 'new') {
		  $form->content(_('New '));
			$form->input('idHasPart', $idHasPart, 'hidden');
    } else {
			$form->input('idpropertyValue',$value['idpropertyValue'], 'hidden');
	  }
		// NAME
	  $form->fieldsetWithInput('name', $value['name'] ?? null, _('Name'));
		// VALUE
	  $form->fieldsetWithInput('value', $value['value'] ?? null, _('Value'));
		// SUBMIT BUTTONS
	  $form->submitButtonSend();
		if ($case == 'edit') $form->submitButtonDelete('/admin/propertyValue/erase');
		return $form->ready();
  }
}
