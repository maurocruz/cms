<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Plinct\PDO\PDOConnect;
use Plinct\Tool\FileSystem\FileSystem;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Request\Server\Helpers\ImageObjectUpload;

class ImageObjectServer
{
  /**
   * @var array
   */
  private array $tablesHasImageObject;
  /**
   * @var ?array
   */
  private static ?array $KEYWORDS_LIST = null;
  /**
   * @var array
   */
  private static array $KEYWORDS = [];
  /**
   * @var ?array
   */
  private static ?array $LIST_LOCATIONS = null;

  /**
   *
   */
  public function setTableHasImageObject() // DEPRECATED
  {
    $table_schema = PDOConnect::getDbname();
    $this->tablesHasImageObject = PDOConnect::run("select table_name as tableName from information_schema.tables WHERE table_schema = '$table_schema' AND table_name LIKE '%_has_imageObject';");
  }

  /**
   * @throws Exception
   */
  public function new($params)
  {
    $responseDataBase = null;

    // IF UPLOAD IMAGE
    if (isset($_FILES['imageupload'])) {
      // set destination
      $location = $params['location'];
      unset($params['location']);

      // upload images
      if ($_FILES['imageupload']['size'][0] !== 0) {
        $newParams = ImageObjectUpload::uploadImages($_FILES['imageupload'], $location);

        foreach ($newParams as $valueNewParams) {
          $params = array_merge($params, $valueNewParams);
          $responseDataBase[] = CmsFactory::request()->api()->post("imageObject", $params)->ready();
        }
      }

    } else {
      // IF CHOOSE MULTIPLE IMAGE FOR TABLE HAS PART
      $idArray = $params['idimageObject'] ?? $params['id'] ?? $params['idArray'];

      if (is_array($idArray)) {
        foreach($idArray as $value) {
          $params['idIsPartOf'] = $value;
					unset($params['idimageObject']);
					unset($params['id']);
					unset($params['idArray']);
          $responseDataBase[] = CmsFactory::request()->api()->post('imageObject', $params)->ready();
        }
      } else {
        $responseDataBase[] = CmsFactory::request()->api()->post("imageObject", $params)->ready();
      }
    }

    if (isset($params['tableHasPart'])) {
      return filter_input(INPUT_SERVER, 'HTTP_REFERER');

    } else {
      if (count($responseDataBase) == 1) {
        return "/admin/imageObject/edit/".$responseDataBase[0]['id'];

      } else {
        return "/admin/imageObject?listBy=keywords&limit=40&offset=0&keywords=".$params['keywords'];
      }
    }
  }

  /**
   * @param $params
   * @return mixed|string
   */
  public function erase($params)
  {
    $n = 0;
    // ERASE TABLE RELATIONSHOP ONLY
    if (isset($params['tableHasPart']) && isset($params['idHasPart']) && isset($params['tableIsPartOf']) && isset($params['idIsPartOf'])) {
      CmsFactory::request()->api()->delete('imageObject', $params)->ready();
      return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    // DELETE REGISTER AND UNLINK IMAGE
    else {
      // delete register
      CmsFactory::request()->api()->delete('imageObject', [ "idimageObject" => $params['idimageObject'] ])->ready();

      // unlink image
      $imageFile =  $_SERVER['DOCUMENT_ROOT'] . parse_url($params['contentUrl'])['path'];

	    if (file_exists($imageFile)) {
        $n = $this->deleteFiles($imageFile);
      }
      // RESPONSE
      return $n == 0 ? dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) : "/admin/imageObject?listBy=keywords&limit=40&offset=0&keywords=".$params['keywords'];
    }
  }

  /**
   * @param $imageFile
   * @return int
   */
  private function deleteFiles($imageFile): int
  {
    $n = 0;
    $pathinfo = pathinfo($imageFile);
    $dirname = $pathinfo['dirname'];
    $filename = $pathinfo['filename'];

		if (is_dir($dirname) && is_file($imageFile)) {
			$directory = new RecursiveDirectoryIterator($dirname, FilesystemIterator::SKIP_DOTS);
			$iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);

			foreach ($iterator as $file) {
				// UNLINK FILE
				if ($file->isFile() && strstr($file->getFileName(), $filename)) {
					unlink($file->getRealPath());
				}

				// COUNT THE IMAGES ON FOLDER PARENT
				if ($iterator->getDepth() === 0) {
					$n += $file->isFile() ? 1 : 0;
				}
			}

			// REMOVE DIR IF EMPTY
			if ($n == 0) {
				rmdir($dirname . "/thumbs");
				rmdir($dirname);
			}
		}

    return $n;
  }

  /**
   * @param $idIsPartOf
   * @return array|null
   */
  public function getImageHasPartOf($idIsPartOf): ?array // DEPRECATED
  {
    $info = null;
    $this->setTableHasImageObject();

    foreach ($this->tablesHasImageObject as $value) {
	    $tableHasName = $value['tableName'];
	    $tableHasPart = strstr($value['tableName'], "_", true);

	    if ($tableHasPart !== 'group') {
		    $query = "select * from `$tableHasName`, `$tableHasPart` WHERE `idimageObject`=$idIsPartOf AND $tableHasPart.id$tableHasPart=$tableHasName.id$tableHasPart;";
		    $data = PDOConnect::run($query);

		    if (!isset($data['error']) && count($data) > 0) {
			    foreach ($data as $valueTableIspartOf) {
				    $id = $valueTableIspartOf["id$tableHasPart"];
				    $info[] = ["tableHasPart" => $tableHasPart, "idHasPart" => $id, "values" => $valueTableIspartOf];
			    }
		    }
      }
    }

    return $info;
  }

  /**
   * @param $directory
   * @param null $relative
   * @return array
   */
  public static function listLocation($directory, $relative = null): array
  {
    self::$LIST_LOCATIONS = self::$LIST_LOCATIONS ?? FileSystem::listDirectories($directory);
    if ($relative) {
      $newList = [];
      foreach (self::$LIST_LOCATIONS as $item) {
        $newItem = str_replace(App::getImagesFolder(),'',$item);
        if($newItem != '') {
          $newList[] = $newItem;
        }
      }
      self::$LIST_LOCATIONS = $newList;
    }

    return self::$LIST_LOCATIONS;
  }

  /**
   * @return array
   */
  public static function listKeywords(): array
  {
    self::$KEYWORDS_LIST = self::$KEYWORDS_LIST ?? CmsFactory::request()->api()->get("ImageObject", [ "fields"=>"distinct(keywords)", "groupBy" => "keywords", "orderBy" => "keywords" ])->ready();

    if(self::$KEYWORDS === []) {
      foreach (self::$KEYWORDS_LIST as $value) {
        self::$KEYWORDS[] = $value['keywords'];
      }
    }

    return self::$KEYWORDS;
  }
}
