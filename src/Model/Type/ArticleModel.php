<?php
namespace Plinct\Cms\Model\Type;

use DateTime;

class ArticleModel implements TypeModelInterface
{

	/**
	 * @inheritDoc
	 */
	public function create(array $params): array
	{
		return $params;
	}

	/**
	 * @inheritDoc
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

	/**
	 * @inheritDoc
	 */
	public function delete(array $params): array
	{
		return $params;
	}
}
