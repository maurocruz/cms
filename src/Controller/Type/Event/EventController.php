<?php
namespace Plinct\Cms\Controller\Type\Event;

use DateTime;
use DOMException;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\Type\TypeControllerInterface;
use Plinct\Tool\Sitemap;
use Plinct\Tool\ToolBox;

class EventController implements TypeControllerInterface
{

	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('event')->ready();
	}

	public function new(array $params): bool
	{
		return false;
	}
	/**
   * @param array $params
   * @return bool
	 */
	public function edit(array $params): bool
	{
		$idevent = $params['idevent'];
		$data = CmsFactory::model()->type('event')->get(['idevent'=>$idevent,'properties'=>'location']);
	  return CmsFactory::view()->webSite()->type('event')->setData($data)->setMethodName('edit')->ready();
	}

	/**
	 * @throws DOMException
	 * @throws DOMException
	 */
	public function sitemap(?array $params): ?bool
	{
		$template = $params['loc'] ?? null;
		$dataSitemap = null;
		$params = [ "orderBy" => "dateCreated", "ordering" => "desc", 'limit'=>'none' ];
		$data = CmsFactory::model()->type('event')->get($params);
		foreach ($data as $value) {
			$typeBuider = ToolBox::typeBuilder($value);
			$idevent = $typeBuider->getId();
			$startDate = $value['startDate'];
			$value['startDate'] = (new DateTime($startDate))->format('Y-m-d');
			$name = $value['name'];
			$value['name'] = str_replace(" ","+",$name);
			$dateCreated = $typeBuider->getPropertyValue('dateCreated');
			$dateModified = $typeBuider->getPropertyValue('dateModified');
			$loc = $template ?
				preg_replace_callback('/\[(.*?)]/', function ($matches) use ($value) {
					$key = $matches[1]; // Obtém o texto dentro dos colchetes
					return isset($value[$key]) ? urlencode($value[$key]) : $matches[0]; // Substitui pelo valor ou mantém o original
				}, $template)
				: App::getURL() . DIRECTORY_SEPARATOR . "t" . DIRECTORY_SEPARATOR . "article" . DIRECTORY_SEPARATOR . $idevent;
			if ($dateCreated && $dateModified && $loc) {
				$dataSitemap[] = [
					"loc" => $loc,
					'lastmod' => \Plinct\Tool\DateTime::formatISO8601($dateModified),
					"news" => [
						"name" => App::getTitle(),
						"language" => App::getLanguage(),
						"publication_date" => \Plinct\Tool\DateTime::formatISO8601($dateCreated),
						"title" => $name
					]
				];
			}
		}
		return (new Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-event.xml"))->saveSitemap($dataSitemap, "news");
	}
}
