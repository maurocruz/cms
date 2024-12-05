<?php
namespace Plinct\Cms\Controller\Type;

interface TypeControllerInterface
{
	/**
	 * @param array $params
	 * @return bool
	 */
	public function index(array $params): bool;

	/**
	 * @param array $params
	 * @return bool
	 */
	public function new(array $params): bool;

	/**
	 * @param array $params
	 * @return bool
	 */
	public function edit(array $params): bool;
}
