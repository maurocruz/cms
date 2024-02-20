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

}
