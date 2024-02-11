<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Form;

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
}