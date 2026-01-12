<?php
namespace Plinct\Cms\Controller\Authentication;

use Plinct\Cms\CmsFactory;
use Psr\Http\Message\ServerRequestInterface;

class Authentication
{
	public function register( ServerRequestInterface $request): array
	{
		$params = $request->getParsedBody();
		unset($params['submit']);
		$data = CmsFactory::model()->api()->post('auth/register', $params)->ready();
		if (isset($data['status'])) {
			if ($data['status'] === 'error') {
				if (isset($data['data'])) {
					CmsFactory::view()->Logger('auth')->notice('REGISTER FAILED: duplicate email', (array)$data['data']);
				}
			}
		}
		return $data;
	}
}
