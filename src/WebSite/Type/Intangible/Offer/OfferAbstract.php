<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\Offer;

use Plinct\Cms\View\Widget\FormElementsTrait;

abstract class OfferAbstract
{
    /**
     * @var array
     */
    protected array $content = [];
    /**
     * @var string
     */
    protected string $tableHasPart;
    /**
     * @var int
     */
    protected int $idHasPart;
    /**
     * @var array
     */
    protected array $offeredBy;

    use FormElementsTrait;

    /**
     * @param array $data
     */
    public function setOfferedBy(array $data): void
    {
        $this->offeredBy = $data['@type'] == "Product" ? $data['manufacturer'] : ($data['@type'] == "Service" ? $data['provider'] : null);
    }
}
