<?php
namespace Plinct\Cms\Controller\Type\Article;

use DateTime;
use DOMException;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\Type\TypeControllerInterface;
use Plinct\Tool\Sitemap;
use Plinct\Tool\ToolBox;

class ArticleController implements TypeControllerInterface
{
	/**
	 * @param array $params
	 * @return bool
	 */
  public function edit(array $params): bool
  {
    $params2 = [ "properties" => "*" ];
    $params3 = $params ? array_merge($params, $params2) : $params2;
		$data = CmsFactory::model()->api()->get("article", $params3)->ready();
		return CmsFactory::view()->webSite()->type('article')->setData($data)->setMethodName('edit')->ready();
  }

	/**
	 * @param array $params
	 * @return array
	 */
	public function update(array $params): array
	{
		$creativeWorkStatus = $params['creativeWorkStatus'];
		$datePublished = $params['datePublished'];
		if ($creativeWorkStatus == 'published' && ($datePublished == '' || $datePublished == '00-00-00 00:00:00')) {
			$params['datePublished'] = (new DateTime())->format('Y:m:d h:i:s');
		} else if($creativeWorkStatus !== 'published') {
			$params['datePublished'] = '';
		}
		return $params;
	}

	public function index(array $params): bool
	{
		return false;
	}

	public function new(array $params): bool
	{
		return false;
	}

	/**
	 * @throws DOMException
	 */
	public function sitemap(?array $params): ?bool
	{
		$template = $params['loc'] ?? null;
		$dataSitemap = null;
		$params = ['fields'=>'headline,datePublished', "orderBy" => "datePublished", "ordering" => "desc", 'limit'=>'none' ];
		$data = CmsFactory::model()->type('article')->get($params);
		foreach ($data as $value) {
			$typeBuider = ToolBox::typeBuilder($value);
			$diarticle = $typeBuider->getId();
			$datePublished = $value['datePublished'];
			$value['datePublished'] = (new DateTime($datePublished))->format('Y-m-d');
			$headline = $value['headline'];
			$value['headline'] = str_replace(" ","+",$headline);
			$dateModified = $typeBuider->getPropertyValue('dateModified');
			$loc = $template ?
				preg_replace_callback('/\[(.*?)]/', function ($matches) use ($value) {
					$key = $matches[1]; // Obtém o texto dentro dos colchetes
					return $value[$key] ?? $matches[0]; // Substitui pelo valor ou mantém o original
				}, $template)
				: App::getURL() . DIRECTORY_SEPARATOR . "t" . DIRECTORY_SEPARATOR . "article" . DIRECTORY_SEPARATOR . $diarticle;
			if ($datePublished) {
				$dataSitemap[] = [
					"loc" => $loc,
					'lastmod' => \Plinct\Tool\DateTime::formatISO8601($dateModified),
					"news" => [
						"name" => App::getTitle(),
						"language" => App::getLanguage(),
						"publication_date" => \Plinct\Tool\DateTime::formatISO8601($datePublished),
						"title" => $headline
					]
				];
			}
		}
		return (new Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-article.xml"))->saveSitemap($dataSitemap, "news");
	}
}
