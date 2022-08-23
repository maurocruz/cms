<?php

declare(strict_types=1);

namespace Plinct\Cms\Response\View\Fragment;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Response\Fragment\Box\BoxInterface;
use Plinct\Cms\Response\Fragment\Error\ErrorInterface;
use Plinct\Cms\Response\Fragment\Form\Form;
use Plinct\Cms\Response\Fragment\ListTable\ListTableInterface;
use Plinct\Cms\Response\Fragment\Miscellaneous\MiscellaneousInterface;
use Plinct\Cms\Response\Fragment\Navbar\NavbarFragmentInterface;
use Plinct\Web\Fragment\Icons\IconsFragment;

class Fragment
{
	public static function icon(): IconsFragment
	{
		return CmsFactory::response()->fragment()->icon();
	}

	public static function listTable(array $attributes = null): ListTableInterface
	{
		return CmsFactory::response()->fragment()->listTable($attributes);
	}

	public static function box(): BoxInterface
	{
		return CmsFactory::response()->fragment()->box();
	}

	public static function form(array $attributes = null): Form
	{
		return CmsFactory::response()->fragment()->form($attributes);
	}

	public static function navbar(): NavbarFragmentInterface
	{
		return CmsFactory::response()->fragment()->navbar();
	}

	public static function miscellaneous(): MiscellaneousInterface
	{
		return CmsFactory::response()->fragment()->miscellaneous();
	}

	public static function noContent($message = 'no content'): array
	{
		return CmsFactory::response()->fragment()->noContent($message);
	}
	public static function error(): ErrorInterface
	{
		return CmsFactory::response()->fragment()->error();
	}
}