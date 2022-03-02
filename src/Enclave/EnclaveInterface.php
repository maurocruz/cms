<?php

declare(strict_types=1);

namespace Plinct\Cms\Enclave;

interface EnclaveInterface
{
    /**
     * @return string
     */
    public function getMenuPath(): string;

    /**
     * @return string
     */
    public function getMenuText(): string;

    /**
     * @return array
     */
    public function navBar(): array;

    /**
     * @param $queryParams
     * @return array
     */
    public function viewMain($queryParams = null): array;

    /**
     * @param array $params
     * @return array|string
     */
    public function post(array $params);

    /**
     * @param array $params
     * @return array|string
     */
    public function put(array $params);

    /**
     * @param array $params
     * @return array|string
     */
    public function delete(array $params);
}
