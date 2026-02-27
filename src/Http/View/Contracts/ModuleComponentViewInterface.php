<?php
namespace Plinct\Cms\Http\View\Contracts;

use Plinct\Cms\Http\View\Component\Form\Form;

interface ModuleComponentViewInterface
{
	public static function navbar(array $querystrings = null): array;
	public static function navbarItem(string $name, string $id, array $querystrings = null): array;
	public static function navbarParent(string $name, string $id, string $nameParent, string $idparent, array $queryStrings = null ): array;
	public static function form(Form $form, string $case = 'new', array $value = null): array;
	public static function formFragment(Form $form, array $value = null): Form;
}
