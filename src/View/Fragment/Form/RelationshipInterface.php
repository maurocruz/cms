<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Form;

use Plinct\Web\Element\Form\FormInterface;

interface RelationshipInterface
{
	/**
	 * @param string $propertyName
	 * @param array|null $value
	 * @param string|null $orberBy
	 * @return array
	 */
	public function oneToOne(string $propertyName,array $value = null, string $orberBy = null): array;

	/**
	 * @param array|null $value
	 * @param string|null $orberBy
	 * @return array
	 */
	public function oneToMany(array $value = null, string $orberBy = null): array;

	/**
	 * @param string $type
	 * @param string $legend
	 * @param string $propertyName
	 * @param int|null $value
	 * @return FormInterface
	 */
	public function relationshipOneToOne(string $type, string $legend, string $propertyName, int $value = null): FormInterface;
}
