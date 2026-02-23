<?php
namespace Plinct\Cms\Http\View\Component\Form;

use Plinct\Web\Element\Form\FormInterface;

interface RelationshipInterface
{
	/**
	 * @param string $type
	 * @param string $legend
	 * @param string $propertyName
	 * @param int|null $value
	 * @return FormInterface
	 */
	public function relationshipOneToOne(string $type, string $legend, string $propertyName, int $value = null): FormInterface;
}
