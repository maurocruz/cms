<?php
namespace Plinct\Cms\View\Fragment;

use Plinct\Cms\View\Fragment\Box\Box;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\Fragment\Miscellaneous\Miscellaneous;
use Plinct\Cms\View\Fragment\Table\Table;
use Plinct\Cms\View\Fragment\Table\TableInterface;

class CmsFragment
{
	/**
	 * @return Box
	 */
	public static function box(): Box
	{
		return new Box();
	}

	/**
	 * @param string $formName
	 * @param array|null $attributes
	 * @return Form
	 */
	public static function form(string $formName, array $attributes = null): Form {
		return new Form($formName, $attributes);
	}

	/**
	 * @param string|null $message
	 * @return array
	 */
	public static function noContent(string $message = null): array {
		$misc = new Miscellaneous();
		$mess = $message ?? _("No content");
		return $misc->message($mess);
	}

	/**
	 * @param array|null $attributes
	 * @return TableInterface
	 */
	public static function table(array $attributes = null): TableInterface {
		return new Table($attributes);
	}

}
