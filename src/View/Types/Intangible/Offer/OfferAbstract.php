<?php
namespace Plinct\Cms\View\Types\Intangible\Offer;

use Plinct\Cms\View\Widget\FormElementsTrait;

abstract class OfferAbstract {
    protected $content = [];
    protected $tableHasPart;
    protected $idHasPart;
    protected $offeredBy;

    use FormElementsTrait;

    public function setOfferedBy(array $data): void {
        $this->offeredBy = $data['@type'] == "Product" ? $data['manufacturer'] : ($data['@type'] == "Service" ? $data['provider'] : null);
    }
}