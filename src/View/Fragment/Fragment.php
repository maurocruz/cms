<?php
namespace Plinct\Cms\View\Fragment;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Helpers\Sitemap;
use Plinct\Cms\View\Authentication\AuthFragment;
use Plinct\Cms\View\Fragment\Box\Box;
use Plinct\Cms\View\Fragment\Button\Buttons;
use Plinct\Cms\View\Fragment\Form\Form;
use Plinct\Cms\View\Fragment\Navbar\NavbarRow;
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
	 * @param string $contentUrl
	 * @param array|null $attributes
	 * @return string
	 */
	public function audio(string $contentUrl, ?array $attributes = null): string
	{
		$attr = CmsFactory::toolBox()->convertAttributesToString($attributes);
		return "<audio src='$contentUrl' $attr controls></audio>";
	}
  /**
   * @return AuthFragment
   */
  public function auth(): AuthFragment {
    return new AuthFragment();
  }

  /**
   * @return Box
   */
  public function box(): Box
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
	 * @return NavbarRow
	 */
	public function navbarRow(): NavbarRow
	{
		return new NavbarRow();
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
	public function sitemap(string $type): Sitemap
	{
		return new Sitemap($type);
	}

	/**
	 * @param bool $boolean
	 * @param string $filename
	 * @return void
	 */
	public function sitemapReturn(bool $boolean, string $filename): void
	{
		CmsFactory::view()->addMain("<h1>Sitemap</h1>");
		if ($boolean) {
			CmsFactory::view()->addMain("<p class='warning'>Sitemap criado com sucesso!</p><p><a href='/$filename' target='_blank'>Ver sitemap.</a></p>");
		} else {
			CmsFactory::view()->addMain("<p class='warning'>Ops! Algo de ruim aconteceu! O sitemap não foi criado!</p>");
		}
	}

	/**
	 * @param string $contentUrl
	 * @param array|null $attributes
	 * @return string
	 */
	public function video(string $contentUrl, ?array $attributes = null): string
	{
		$attr = CmsFactory::toolBox()->convertAttributesToString($attributes);
		return "<video $attr controls><source src='$contentUrl' type='video/mp4'></video>";
	}
}
