<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;

class ArticleView extends CreativeWorkView
{
	/**
	 * @param string $type
	 * @param string $sitemapFilename
	 */
	public function __construct(string $type = 'article', string $sitemapFilename = 'sitemap-article.xml')
	{
		$this->sitemapExtension = 'news';
		parent::__construct($type, $sitemapFilename);
	}

	public function __destruct()
	{
		parent::__destruct();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar(_("Article"), [
				"/admin/article" => CmsFactory::view()->fragment()->icon()->home(),
				"/admin/article/new" => CmsFactory::view()->fragment()->icon()->plus(),
				"/admin/article/sitemap" => CmsFactory::view()->fragment()->icon()->sitemap()
			])
				->level(3)
				->type('Article')
				->ready()
		);
	}

	/**
	 * @param string|null $title
	 * @return void
	 */
  protected function navbarArticle(string $title = null): void
  {
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar(_("Article"), [
		      "/admin/article" => CmsFactory::view()->fragment()->icon()->home(),
		      "/admin/article/new" => CmsFactory::view()->fragment()->icon()->plus()
	      ])
				->type('Article')
				->ready()
		);
		//
    if ($title) {
			CmsFactory::view()->addHeader(
        CmsFactory::view()->fragment()->navbar($title, [], 3)->ready()
			);
    }
  }

  /**
   *
   * @param array|null $data
   * @param array|null $queryParams
   */
  public function index(?array $data, array $queryParams = null): void
  {
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('article')->setColumnsTable(['headline'=>_('Title'),'creativeWorkStatus'=>_("Creative work status"), 'datePublished'=> _('Date published')])->setOrderBy('datePublished')->ready());
  }

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @param
	 */
	public function new(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formArticle(),_("Article")));
	}

  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @throws Exception
   */
  public function edit(?array $data, array $queryParams = null): void
  {
		if (isset($data[0])) {
      $value = $data[0];
			$typeBuilder = new TypeBuilder('article', $value);
			$idarticle = $typeBuilder->getId();
			$this->idthing = (int) $typeBuilder->getPropertyValue('idthing');
			$this->name = $typeBuilder->getValue('headline');
      if (empty($value)) {
        $content[] = CmsFactory::view()->fragment()->noContent();
      } else {
        $content[] = CmsFactory::view()->fragment()->box()->simpleBox(self::formArticle("edit", $value, $idarticle), _("Article"), $this->idthing, $this->name);
        // images
	      $content[] = CmsFactory::view()->fragment()->reactShell('imageObject')->setProperty('hasPart')->setIdHasPart($this->idthing)->ready();
      }
    } else {
      $this->navbarArticle();
      $content[] = CmsFactory::view()->fragment()->noContent(_("No articles were found!"));
    }
    CmsFactory::view()->addMain($content);
  }

  /**
   * @param string $case
   * @param null $value
   * @param null $ID
   * @return array
   */
  private function formArticle(string $case = "new", $value = null, $ID = null): array
  {
    $articleBody = isset($value['articleBody']) ? stripslashes($value['articleBody']) : null;
		$backStory = $value['backStory'] ?? null;
		// FORM
    $form = CmsFactory::view()->fragment()->form("form-article",["class"=>"form-basic form-article"]);
    $form->action("/admin/article/$case")->method('post');
		$form->setIdform($case == 'new' ? "form-article-new" : "form-article-".$ID);
		$form->addMandatories(['headline','articleBody']);
    // id
    if ($case == "edit") $form->input('idarticle', (string) $ID, 'hidden');
		// creativeWorl form
	  $form = parent::formCreativeWorkContent($form, $value);
    // article body
	  $form->content(CmsFactory::view()->fragment()->box()->expandingBox(
			_('Article body'),
			"<textarea name='articleBody' class='article-articleBody' id='articleBody$ID'>$articleBody</textarea>", false, 'width: 100%;'));
    $form->setEditor("articleBody$ID", "editor$case$ID");
    // article section
    $form->fieldsetWithInput("articleSection", $value['articleSection'] ?? null, _("Article sections") );
		// back story
	  $form->fieldsetWithTextarea("backStory", $backStory, _("Back story"));
    // submit
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete("/admin/article/erase");
    return $form->ready();
  }
}
