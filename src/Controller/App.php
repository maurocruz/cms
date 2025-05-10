<?php
namespace Plinct\Cms\Controller;

use Exception;
use Gitonomy\Git\Repository;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
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
   * @var ?string
   */
  private static ?string $IMAGES_FOLDER = "/public/images/";
	/**
	 * @var int
	 */
  private static int $IMAGE_MAX_WIDTH = 1080;
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
   * @var string
   */
  private static string $API_SECRET_KEY = "";
  /**
   * @var float|int
   */
  private static int|float $API_USER_EXPIRE = 60*60*24*7;
  /**
   * @var string
   */
  private static string $STATIC_FOLDER = "https://plinct.com.br/static/dist/";
  /**
   * @var
   */
  private static mixed $soloineUrl;
  /**
   * @var bool
   */
  private static bool $richTextEditor = true;
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

	private static bool $isRemoteApi = true;

	public static string $DB_NAME;
  /**
   * @param Slim $slim
   */
  public function __construct(Slim $slim)
  {
    $this->slim = $slim;
		// BASE DIR
		self::setBASEDIR(realpath(__DIR__ . '/../..'));
		// URL
		$host = filter_input(INPUT_SERVER, 'HTTP_HOST');
		self::setURL((filter_input(INPUT_SERVER, 'HTTPS') == 'on' ? "https" : "http") . ":" . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $host);
		// TITLE
		$this->setTitle($host);
		// VERSION
    self::setVersion();
		// LANGUAGE
    self::$LANGUAGE = Locale::getServerLanguage();
  }

	/**
	 * @return string
	 */
	public static function getDBNAME(): string
	{
		return self::$DB_NAME;
	}

	/**
	 * @param $driver
	 * @param $host
	 * @param $dbname
	 * @param $username
	 * @param $password
	 * @param array $options
	 */
	public function connect($driver, $host, $dbname, $username, $password, array $options = []): void
	{
		self::$DB_NAME = $dbname;
		PDOConnect::connect($driver, $host, $dbname, $username, $password, $options);
	}

	/**
	 * @param string $BASE_DIR
	 */
	public static function setBASEDIR(string $BASE_DIR): void
	{
		self::$BASE_DIR = $BASE_DIR;
	}

	/**
	 * @param string $URL
	 */
	public static function setURL(string $URL): void
	{
		self::$URL = $URL;
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
  public function setSoloineUrl(mixed $soloineUrl): void
  {
    self::$soloineUrl = $soloineUrl;
  }

  /**
   * @return mixed
   */
  public static function getSoloineUrl(): mixed
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
  public static function setVersion(): void
  {
		$version = 'NAN';
	  $gitDirectory = realpath(__DIR__ . '/../../.git');
	  if ($gitDirectory) {
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
		  $version = "Working in local. Branch: <b>$branch</b>; Version: <b>$versionTag</b>";

	  } else {
		  $installedFile = realpath($_SERVER['DOCUMENT_ROOT'] . "/../vendor/composer/installed.json");
		  $packages = json_decode(file_get_contents($installedFile));
		  foreach ($packages->packages as $package) {
			  if ($package->name == "plinct/cms") {
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
			return str_ends_with(self::$API_HOST, "/") ? self::$API_HOST : self::$API_HOST. "/";
		}
		return null;
  }

	/**
	 * @return string
	 */
  public static function getApiSecretKey(): string
  {
    return self::$API_SECRET_KEY;
  }

  /**
   * @return float|int
   */
  public static function getApiUserExpire(): float|int
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
	 * @return bool
	 */
	public static function isRemoteApi(): bool
	{
		return self::$isRemoteApi;
	}

	/**
	 * @throws Exception
	 */
	private function setConfig(): void
	{
		self::$isRemoteApi = App::getApiHost() == App::getURL().'/api/';
		$configurationItems = CmsFactory::model()->api()->get('config')->ready();
		// CONFIGURATION ITEMS
		$itemListElement = is_array($configurationItems['itemListElement']) ? $configurationItems['itemListElement'] : [];
		foreach ($itemListElement as $value) {
			$item = $value['item'];
			if ($item['name'] == 'Modules Available') {
				CmsFactory::controller()->configuration()->setModulesAvailable($item['itemListElement']);
			}
			if ($item['name'] == 'Modules Enabled') {
				CmsFactory::controller()->configuration()->setModulesEnabled($item['itemListElement']);
			}
		}
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
  final public function run(): mixed
  {
		$this->setConfig();

		return CmsFactory::controller()->Routes()->home($this->slim);
  }
}
