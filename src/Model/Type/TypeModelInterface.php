<?php
namespace Plinct\Cms\Model\Type;

interface TypeModelInterface
{
	/**
	 * @param array $params
	 * @return array
	 */
	public function create(array $params): array;

	/**
	 * @param array $params
	 * @return array
	 */
	public function update(array $params): array;

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array;
}
