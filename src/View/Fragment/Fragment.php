<?php
namespace Plinct\Cms\View\Fragment;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\Authentication\AuthFragment;
use Plinct\Cms\View\Fragment\Box\Box;
use Plinct\Cms\View\Fragment\Box\BoxInterface;
use Plinct\Cms\View\Fragment\Button\Buttons;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\Fragment\Table\Table;
use Plinct\Cms\View\Fragment\Table\TableInterface;
use Plinct\Cms\View\Fragment\Message\Message;
use Plinct\Cms\View\Fragment\Miscellaneous\Miscellaneous;
use Plinct\Cms\View\Fragment\Miscellaneous\MiscellaneousInterface;
use Plinct\Cms\View\Fragment\Navbar\Navbar;
use Plinct\Cms\View\Fragment\Navbar\NavbarInterface;
use Plinct\Cms\View\Fragment\ReactShell\ReactShell;
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
	 * @param string $formName
	 * @param array|null $attributes
	 * @return Form
	 */
  public function form(string $formName, array $attributes = null): Form {
    return new Form($formName, $attributes);
  }

	/**
	 * @return IconsFragment
	 */
  public function icon(): IconsFragment {
		return \Plinct\Web\Fragment\Fragment::icons();
  }

  /**
   * @param array|null $attributes
   * @return TableInterface
   */
  public function table(array $attributes = null): TableInterface {
    return new Table($attributes);
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
	 * @param string|null $title
	 * @param array|null $list
	 * @param int $level
	 * @param array|null $searchInput
	 * @return NavbarInterface
	 */
  public function navbar(string $title = null, array $list = null, int $level = 2, array $searchInput = null): NavbarInterface {
    return new Navbar($title, $list, $level, $searchInput);
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

	/**
	 * @param string $type
	 * @param array $attributes
	 * @return ReactShell
	 */
	public function reactShell(string $type, array $attributes = []): ReactShell
	{
		return new ReactShell($type, $attributes);
	}

	/**
	 * @throws Exception
	 */
	public function sitemap(string $type, array $params = null): bool
	{
		CmsFactory::view()->addMain("<h1>Sitemap</h1>");
		$sitemap = CmsFactory::helpers()->sitemap($type);
		$sitemap->setParams($params);
		if ($sitemap->saveSitemap()) {
			CmsFactory::view()->addMain("<p class='warning'>Sitemap criado com sucesso!</p><p><a href='".$sitemap->getCurrentSitemap()."' target='_blank'>Ver sitemap.</a></p>");
			return true;
		} else {
			CmsFactory::view()->addMain("<p class='warning'>Ops! Algo de ruim aconteceu! O sitemap não foi criado!</p>");
			return false;
		}
	}
}
