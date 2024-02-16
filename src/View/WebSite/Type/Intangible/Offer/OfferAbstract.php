<?php

declare(strict_types=1);

namespace Plinct\Cms\View\WebSite\Type\Intangible\Offer;

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
     * @var string
     */
    protected string $idHasPart;
    /**
     * @var array
     */
    protected array $offeredBy;

    /**
     * @param array $data
     */
    public function setOfferedBy(array $data): void
    {
        $this->offeredBy = $data['@type'] == "Product" ? $data['manufacturer'] : ($data['@type'] == "Service" ? $data['provider'] : null);
    }
}
