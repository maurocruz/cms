<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Authentication;

use Plinct\Cms\CmsFactory;
use Psr\Http\Message\ServerRequestInterface;

class Authentication
{
	public function register( ServerRequestInterface $request)
	{
		$params = $request->getParsedBody();
		unset($params['submit']);
		$data = CmsFactory::model()->api()->post('auth/register', $params)->ready();
		if (isset($data['status'])) {
			if ($data['status'] === 'error') {
				if (isset($data['data'])) {
					CmsFactory::view()->Logger('auth')->notice('REGISTER FAILED: duplicate email', $data['data']);
				}
			}
		}
		return $data;
	}
}