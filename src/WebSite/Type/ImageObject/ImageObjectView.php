<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\ImageObject;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Tool\ArrayTool;

class ImageObjectView extends ImageObjectWidget
{
	/**
	 * @param array $data
	 * @param $params
	 */
  public function index(array $data, $params)
  {
		$listBy = $params['listBy'] ?? null;

		parent::navBarLevel1();
		if ($listBy === 'groups') {
			// TODO UNDER DEVELOPMENT
			CmsFactory::webSite()->addMain('<p>Under development!</p>');
		} else {
			//$apiHost = App::getApiHost();
			//CmsFactory::webSite()->addMain("<script src='https://plinct.com.br/static/dist/plinct-imageObject/main.cb79621f47fd3a6ac896.js'></script>");
			//CmsFactory::webSite()->addMain("<div id='plinctImageObject' data-apiHost='$apiHost'></div>");
			CmsFactory::webSite()->addMain('<div id="imageGrid"></div><script src="/App/static/cms/js/dist/imageObject.bundle.js"></script>');
		}
  }

  /**
   * @param null $data
   */
  public function new($data = null)
  {
		// NAVBAR
		parent::navBarLevel2(_('Add'));
    CmsFactory::webSite()->addMain(self::upload($data['listLocation'] ?? null, $data['listKeywords'] ?? null));
  }

  /**
   * @throws Exception
   */
  public function edit(array $data)
  {
    if (!empty($data)) {
      $id = ArrayTool::searchByValue($data['identifier'], "id")['value'];
      $contentUrl = $data['contentUrl'];
      $this->navBarLevel2(_("Image") . ": $contentUrl");
      // edit image
      $content[] = CmsFactory::response()->fragment()->box()->simpleBox([
        self::formImageObjectEdit($data),
        self::infoIsPartOf($data)
      ], _("Image"));
      // author
      $content[] = CmsFactory::response()->fragment()->box()->expandingBox(_("Author"), [ CmsFactory::response()->fragment()->form()->relationshipOneToOne("ImageObject", $id, "author", "Person", $data['author'])]);
      $content[] = CmsFactory::response()->fragment()->icon()->arrowBack();

    } else {
      $this->navBarLevel2(_("Image not founded!"));
      $content[] = CmsFactory::response()->fragment()->noContent(_("Item not founded"));
    }

    CmsFactory::webSite()->addMain($content);
  }

  /**
   * @throws Exception
   */
  public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array
  {
    $this->tableHasPart = $tableHasPart;
    $this->idHasPart = $idHasPart;

    // form for edit
    $content[] = self::editWithPartOf($data ?? []);
    // upload
    $content[] = self::upload($tableHasPart, $idHasPart);
    // save with a database image
    $content[] = self::addImagesFromDatabase($tableHasPart, $idHasPart);

    return $content;
  }

}
