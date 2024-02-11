<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller;

use Gitonomy\Git\Repository;
use Plinct\Cms\CmsFactory;
use Plinct\Tool\Locale;
use Slim\App as Slim;

/**
 * Class App
 * @package Plinct\Cms
 */
class App
{
	private static string $BASE_DIR;
  /**
   * @var string
   */
  private static string $IMAGES_FOLDER = "/public/images/";
  /**
   * @var string
   */
  private static $IMAGE_MAX_WIDTH = 1080;
  /**
   * @var Slim
   */
  private Slim $slim;
  /**
   * @var string|null
   */
  private static ?string $TITLE = null;
  /**
   * @var string
   */
  private static string $LANGUAGE;
  /**
   * @var array
   */
  private static array $TypesEnabled = [];
  /**
   * @var string
   */
  private static string $VERSION;
  /**
   * @var string
   */
  private static string $URL;
  /**
   * @var string|null
   */
  private static ?string $API_HOST = null;
  /**
   * @var string|null
   */
  private static string $API_SECRET_KEY = "";
  /**
   * @var float|int
   */
  private static $API_USER_EXPIRE = 60*60*24*7;
  /**
   * @var string
   */
  private static string $STATIC_FOLDER = "/App/static/cms/";
  /**
   * @var
   */
  private static $soloineUrl;
  /**
   * @var bool
   */
  private static bool $richTextEditor = false;
	/**
	 * @var string|null
	 */
  private static ?string $mailHost = null;
	/**
	 * @var string|null
	 */
  private static ?string $mailUsername = null;
	/**
	 * @var string|null
	 */
  private static ?string $mailpassword = null;
	/**
	 * @var string|null
	 */
  private static ?string $urlToResetPassword = null;
	/**
	 * @var string|null
	 */
	private static ?string $logdir = null;
  /**
   * @param Slim $slim
   */
  public function __construct(Slim $slim)
  {
    $this->slim = $slim;
		self::$BASE_DIR = realpath(__DIR__ . '/../..');
		$host = filter_input(INPUT_SERVER, 'HTTP_HOST');
    self::$URL = (filter_input(INPUT_SERVER, 'HTTPS') == 'on' ? "https" : "http") . ":" . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . filter_input(INPUT_SERVER,'HTTP_HOST');
		$this->setTitle($host);
    self::setVersion();
    self::$LANGUAGE = Locale::getServerLanguage();
  }
	/**
	 * @param string|null $logdir
	 */
	public function setLogdir(?string $logdir): void
	{
		self::$logdir = $logdir;
	}
	/**
	 * @return string|null
	 */
	public static function getLogdir(): ?string
	{
		return self::$logdir;
	}

	/**
	 * @return string
	 */
	public static function getBASEDIR(): string
	{
		return self::$BASE_DIR;
	}

  /**
   * @return string
   */
  public static function getURL(): string
  {
    return self::$URL;
  }

  /**
   * @param string $apiUrl
   * @param string|null $apiSecretKey
   * @return $this
   */
  public function setApi(string $apiUrl, ?string $apiSecretKey = null): App
  {
    self::$API_HOST = $apiUrl == "localhost" ? self::$URL . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR : $apiUrl;
    self::$API_SECRET_KEY = $apiSecretKey;
    return $this;
  }

  /**
   * @param bool $richTextEditor
   */
  public function setRichTextEditor(bool $richTextEditor): void
  {
    self::$richTextEditor = $richTextEditor;
  }

  /**
   * @return bool
   */
  public static function getRichTextEditor(): bool
  {
    return self::$richTextEditor;
  }

  /**
   * @param mixed $soloineUrl
   */
  public function setSoloineUrl($soloineUrl): void
  {
    self::$soloineUrl = $soloineUrl;
  }

  /**
   * @return mixed
   */
  public static function getSoloineUrl()
  {
    return self::$soloineUrl;
  }

  /**
   * @param string $STATIC_FOLDER
   * @return $this
   */
  public function setStaticFolder(string $STATIC_FOLDER): App
  {
    self::$STATIC_FOLDER = $STATIC_FOLDER;
    return $this;
  }

  /**
   * @param $language
   * @return $this
   */
  public function setLanguage($language): App
  {
    self::$LANGUAGE = $language;
    return $this;
  }

  /**
   * @param $title
   * @return $this
   */
  public function setTitle($title): App
  {
    self::$TITLE = $title; return $this;
  }

  /**
   * @param array $types
   * @return $this
   */
  public function setTypesEnabled(array $types): App
  {
    self::$TypesEnabled = $types; return $this;
  }

  /**
   * @param $relativePath
   * @return $this
   */
  public function setImagesFolder($relativePath): App
  {
    self::$IMAGES_FOLDER = $relativePath; return $this;
  }

  /**
   * @param int $imageMaxWidth
   */
  public function setImageMaxWidth(int $imageMaxWidth): void
  {
    self::$IMAGE_MAX_WIDTH = $imageMaxWidth;
  }

  /**
   *
   */
  public static function setVersion()
  {
		$version = 'NAN';

	  $gitDirectory = realpath(__DIR__ . '/cms/');

	  if (file_exists($gitDirectory.'/.git')) {
		  $repository = new Repository($gitDirectory);
		  $head = $repository->getHead();
		  $branch = rtrim(preg_replace("/(.*?\/){2}/", '', $head->getRevision()));
		  $commit = $head->getCommitHash();

		  $references = $repository->getReferences();
		  $tags = $references->resolveTags($commit);
		  if (!empty($tags)) {
			  $versionTag = rtrim(preg_replace("/(.*?\/){2}/", '', $tags[0]->getFullname()));
		  } else {
			  $versionTag = substr($commit,0,8);
		  }
		  $version = "working in localhost. Branch: <b>$branch</b>; Version: <b>$versionTag</b>";

	  } else {
		  $installedFile = realpath($_SERVER['DOCUMENT_ROOT'] . "/../vendor/composer/installed.json");
		  $packages = json_decode(file_get_contents($installedFile));
		  foreach ($packages->packages as $package) {
			  if ($package->name && $package->name == "plinct/cms") {
				  $version = $package->version;
			  }
		  }
	  }

	  self::$VERSION = $version;
  }

  /**
   * @return string|null
   */
  public static function getApiHost(): ?string
  {
		if (self::$API_HOST) {
			return substr(self::$API_HOST,-1) === "/" ? self::$API_HOST : self::$API_HOST. "/";
		}
		return null;
  }

  /**
   * @return string|null
   */
  public static function getApiSecretKey(): string
  {
    return self::$API_SECRET_KEY;
  }

  /**
   * @return float|int
   */
  public static function getApiUserExpire()
  {
    return self::$API_USER_EXPIRE;
  }

  /**
   * @return string
   */
  public static function getStaticFolder(): string
  {
    return self::$STATIC_FOLDER;
  }

  /**
   * @return int
   */
  public static function getImageMaxWidth(): int
  {
    return self::$IMAGE_MAX_WIDTH;
  }

  /**
   * @return string|null
   */
  public static function getImagesFolder(): ?string
  {
    return self::$IMAGES_FOLDER;
  }

  /**
   * @return string
   */
  public static function getLanguage(): string
  {
    return self::$LANGUAGE;
  }

  /**
   * @return string|null
   */
  public static function getTitle(): ?string
  {
    return self::$TITLE;
  }

  /**
   * @return array
   */
  public static function getTypesEnabled(): array
  {
    return self::$TypesEnabled;
  }

  /**
   * @return string
   */
  public static function getVersion(): string
  {
    return self::$VERSION;
  }

  /**
   * @param string|null $mailHost
   * @return App
   */
  public function setMailHost(?string $mailHost): App
  {
    self::$mailHost = $mailHost;
    return $this;
  }

  /**
   * @return string|null
   */
  public static function getMailHost(): ?string
  {
    return self::$mailHost;
  }

  /**
   * @param string|null $mailUsername
   * @return App
   */
  public function setMailUsername(?string $mailUsername): App
  {
    self::$mailUsername = $mailUsername;
    return $this;
  }

  /**
   * @return string|null
   */
  public static function getMailUsername(): ?string
  {
    return self::$mailUsername;
  }

  /**
   * @param string|null $mailpassword
   * @return App
   */
  public function setMailpassword(?string $mailpassword): App
  {
    self::$mailpassword = $mailpassword;
    return $this;
  }

  /**
   * @return string|null
   */
  public static function getMailpassword(): ?string
  {
    return self::$mailpassword;
  }

  /**
   * @param string|null $urlToResetPassword
   * @return App
   */
  public function setUrlToResetPassword(?string $urlToResetPassword): App
  {
    self::$urlToResetPassword = $urlToResetPassword;
    return $this;
  }

  /**
   * @return string|null
   */
  public static function getUrlToResetPassword(): ?string
  {
    return self::$urlToResetPassword;
  }

  /**
   * @return mixed
   */
  final public function run()
  {
		return CmsFactory::controller()->Routes()->home($this->slim);
  }
}
