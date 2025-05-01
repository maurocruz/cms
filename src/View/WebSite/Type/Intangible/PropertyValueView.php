<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Intangible;

use Plinct\Cms\CmsFactory;

class PropertyValueView
{
	/**
	 * @param string $typeHasPart
	 * @param string $idHasPart
	 * @param array $data
	 * @return array
	 */
  public function getForm(string $typeHasPart, string $idHasPart, array $data): array {
    foreach ($data as $value) {
			$name = $value['name'] ?? null;
			if ($name !== 'dateCreated' && $name !== 'dateModified' && !str_contains($name,'id')) {
				$content[] = self::formPropertyValue($typeHasPart, $idHasPart, 'edit', $value);
			}
    }
    // new
    $content[] = self::formPropertyValue($typeHasPart, $idHasPart);
    return $content;
  }

	/**
	 * @param string $typeHasPart
	 * @param string $idHasPart
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
  protected function formPropertyValue(string $typeHasPart, string $idHasPart, string $case = "new", array $value = null): array
  {
	  $form = CmsFactory::view()->fragment()->form(["class" => "form-basic form-propertyValue"]);
		$form->action("/admin/propertyValue/$case")->method('post');
		$form->setIdform("form-propertyValue-".($value['idpropertyValue'] ?? "new"));
		$form->addMandatories('name');
	  // HIDDENS
	  $form->input('typeHasPart', $typeHasPart, 'hidden');
	  // NEW
	  if ($case == 'new') {
		  $form->content("<p style='width: 100%; margin: 0;'>"._('New ')."</p>");
			$form->input('idHasPart', $idHasPart, 'hidden');
    } else {
			$tb = CmsFactory::toolBox()::typeBuilder($value);
			$form->input('idpropertyValue',$tb->getId(), 'hidden');
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
