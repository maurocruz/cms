<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment;

use Plinct\Cms\View\Fragment\Authentication\AuthFragment;
use Plinct\Cms\View\Fragment\Box\Box;
use Plinct\Cms\View\Fragment\Box\BoxInterface;
use Plinct\Cms\View\Fragment\Button\Buttons;
use Plinct\Cms\View\Fragment\Error\Error;
use Plinct\Cms\View\Fragment\Error\ErrorInterface;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\Fragment\ListTable\ListTable;
use Plinct\Cms\View\Fragment\ListTable\ListTableInterface;
use Plinct\Cms\View\Fragment\Message\Message;
use Plinct\Cms\View\Fragment\Miscellaneous\Miscellaneous;
use Plinct\Cms\View\Fragment\Miscellaneous\MiscellaneousInterface;
use Plinct\Cms\View\Fragment\Navbar\NavbarFragment;
use Plinct\Cms\View\Fragment\Navbar\NavbarFragmentInterface;
use Plinct\Web\Fragment\Icons\IconsFragment;
use Plinct\Web\Fragment\PageNavigation\PageNavigation;

class Fragment
{
  /**
   * @return AuthFragment
   */
  public function auth(): AuthFragment {
    return new AuthFragment();
  }

  /**
   * @return BoxInterface
   */
  public function box(): BoxInterface
  {
    return new Box();
  }

	/**
	 * @return Buttons
	 */
  public function buttons(): Buttons {
    return new Buttons();
  }
  /**
   * @return ErrorInterface
   */
  public function error(): ErrorInterface {
    return new Error();
  }

  /**
   * @param array|null $attributes
   * @return Form
   */
  public function form(array $attributes = null): Form {
    return new Form($attributes);
  }

  public function icon(): IconsFragment {
		return \Plinct\Web\Fragment\Fragment::icons();
  }

  /**
   * @param array|null $attributes
   * @return ListTableInterface
   */
  public function listTable(array $attributes = null): ListTableInterface {
    return new ListTable($attributes);
  }

	/**
	 * @return Message
	 */
	public function message(): Message
	{
		return new Message();
	}

  /**
   * @return MiscellaneousInterface
   */
  public function miscellaneous(): MiscellaneousInterface {
    return new Miscellaneous();
  }

  /**
   * @return NavbarFragmentInterface
   */
  public function navbar(): NavbarFragmentInterface {
    return new NavbarFragment();
  }

  /**
   * @param string|null $message
   * @return array
   */
  public function noContent(string $message = null): array {
    $misc = new Miscellaneous();
    $mess = $message ?? _("No content");
    return $misc->message($mess);
  }

	/**
	 * @param array|null $attributes
	 * @return PageNavigation
	 */
	public function PageNavigation(array $attributes = null): PageNavigation {
		return new PageNavigation($attributes);
	}
}
