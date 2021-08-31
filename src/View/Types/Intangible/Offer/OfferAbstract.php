<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Intangible\Offer;

use Plinct\Cms\View\Widget\FormElementsTrait;

abstract class OfferAbstract
{
    /**
     * @var array
     */
    protected $content = [];
    /**
     * @var
     */
    protected $tableHasPart;
    /**
     * @var
     */
    protected $idHasPart;
    /**
     * @var
     */
    protected $offeredBy;

    use FormElementsTrait;

    /**
     * @param array $data
     */
    public function setOfferedBy(array $data): void
    {
        $this->offeredBy = $data['@type'] == "Product" ? $data['manufacturer'] : ($data['@type'] == "Service" ? $data['provider'] : null);
    }
}
