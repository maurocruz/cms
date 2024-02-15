<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite;

use Plinct\Cms\View\WebSite\Configuration\Configuration;
use Plinct\Cms\View\WebSite\Index\Index;
use Plinct\Cms\View\WebSite\Type\Type;

class WebSite extends WebSiteFactoryAbstract
{
	/**
	 * @return Configuration
	 */
	public function configuration(): Configuration
	{
		return new Configuration();
	}

	/**
	 * @return Index
	 */
	public function index(): Index
	{
		return new Index();
	}

	/**
	 * @return Type
	 */
	public function type(string $type): Type
	{
		return new Type($type);
	}
	/**
	 * @return Enclave
	 */
	/*public static function enclave(): Enclave {
		return new Enclave();
	}*/

	/**
	 * @param string|null $title
	 * @param array|null $list
	 * @param int|null $level
	 * @param array|null $searchInput
	 */
	/*public function navbar(string $title = null, array $list = null, int $level = null, array $searchInput = null)
	{
		$fragment = CmsFactory::response()->fragment()
			->navbar()
			->title($title)
			->level($level);
		if ($list) {
			foreach ($list as $key => $value) {
				$fragment->newTab($key, $value);
			}
		}

		if ($searchInput) {
			$type = $searchInput['table'] ?? null;
			if($type) $fragment->type($type);
			$fragment->search("/admin/$type/search",$searchInput['searchBy'] ?? "name", $searchInput['params'] ?? null, $searchInput['linkList'] ?? null);
		}

		$this->addHeader($fragment->ready());
	}*/



	/**
	 * @param string $type
	 * @return Type
	 */
	/*public function type(string $type): Type
	{
		return new Type($type);
	}*/

	/**
	 * @param string $message
	 * @return void
	 */
	/*public function warning(string $message) {
		$this->addMain([ "tag" => "p", "attributes" => [ "class" => "warning" ], "content" => $message ]);
	}*/
}
