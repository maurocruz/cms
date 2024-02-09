<?php
/**
 * FACTORY FOR FRAGMENTS HTML FROM CMS PLINCT PROJECT
 */

declare(strict_types=1);

namespace Plinct\Cms\Response\Fragment;

use Plinct\Cms\Authentication\AuthFragment;
use Plinct\Cms\Response\Fragment\Box\Box;
use Plinct\Cms\Response\Fragment\Box\BoxInterface;
use Plinct\Cms\Response\Fragment\Button\Button;
use Plinct\Cms\Response\Fragment\Error\Error;
use Plinct\Cms\Response\Fragment\Error\ErrorInterface;
use Plinct\Cms\Response\Fragment\Form\Form;
use Plinct\Cms\Response\Fragment\ListTable\ListTable;
use Plinct\Cms\Response\Fragment\ListTable\ListTableInterface;
use Plinct\Cms\Response\Fragment\Miscellaneous\Miscellaneous;
use Plinct\Cms\Response\Fragment\Miscellaneous\MiscellaneousInterface;
use Plinct\Cms\Response\Fragment\Navbar\NavbarFragment;
use Plinct\Cms\Response\Fragment\Navbar\NavbarFragmentInterface;
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
  public function box(): BoxInterface {
    return new Box();
  }

  public function button(): Button {
    return new Button();
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
