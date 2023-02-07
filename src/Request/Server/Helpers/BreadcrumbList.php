<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Helpers;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;

class BreadcrumbList
{
  /**
   * @var array
   */
  private array $breadcrumbList;

  /**
   * @param array $itemListElement
   */
  private function setBreadcrumbList(array $itemListElement)
  {
    $this->breadcrumbList = [
      '@context'=>'https://schema.org',
      '@type'=>'BreadcrumbList',
      'itemListElement'=>$itemListElement
    ];
  }

  /**
   * @param string $pageUrl
   * @param string|null $alternativeHeadline
   * @return BreadcrumbList
   */
  public function setPageUrl(string $pageUrl, string $alternativeHeadline = null): BreadcrumbList
  {
    $url = null;
    $itemListElement = null;

    $explodeUrl = explode('/',$pageUrl);

    foreach ($explodeUrl as $key => $value) {
      if (reset($explodeUrl) == '' && reset($explodeUrl) == $value) {
        $item = $this->getItem(App::getURL(),_('Home'));
      } elseif (end($explodeUrl) == $value) {
        $url = $url . DIRECTORY_SEPARATOR . $value;
        $item = self::getItem(App::getURL(), $alternativeHeadline ?? ucfirst($value));
      } else {
        $url = $url . DIRECTORY_SEPARATOR . $value;
        $dataParentPage = CmsFactory::request()->api()->get('webPage',['url'=>$url])->ready();

        if (empty($dataParentPage)) {
          $name = ucfirst($value);
        } else {
          $name = $dataParentPage[0]['alternativeHeadline'];
        }

        $item = self::getItem(App::getURL(), $name);
      }
      $itemListElement[] = $this->getListItem($item, ($key+1));
    }

    $this->setBreadcrumbList($itemListElement);

    return $this;
  }

  /**
   * @param array $item
   * @param int $position
   * @return array
   */
  private function getListItem(array $item, int $position): array
  {
    return ['@type'=>'ListItem','position'=>$position,'item'=>$item];
  }

  /**
   * @param string $id
   * @param string $name
   * @return array
   */
  private static function getItem(string $id, string $name): array
  {
    return ['@id'=>$id,'name'=>$name];
  }

  /**
   * @return string
   */
  public function ready(): string
  {
    return json_encode($this->breadcrumbList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  }
}
