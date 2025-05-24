<?php
namespace Plinct\Cms\Model\Type;

class EventModel implements TypeModelInterface
{
	/**
	 * @param array $params
	 * @return array
	 */
	public function create(array $params): array
	{
		$params['startDate'] = $params['startDate']." ".$params['startTime'];
		$params['endDate'] = $params['endDate']." ".$params['endTime'];
		unset($params['startTime'], $params['endTime']);
		return $params;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function update(array $params): array
	{
		$params['startDate'] = $params['startDate']." ".$params['startTime'];
		$params['endDate'] = $params['endDate']." ".$params['endTime'];
		unset($params['startTime'], $params['endTime']);
		return $params;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return $params;
	}
}
