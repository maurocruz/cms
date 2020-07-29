<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Tool\DateTime;
use Plinct\Api\Type\PropertyValue;

class AdvertisingView
{
    protected $content;
    
    private static $ContractTypes = [ "Não definido", "Hospedagem de Domínio", "Inserção com Vínculo", "Subdomínio", "Banner", "Inserção sem Vínculo" ];

    use \Plinct\Web\Widget\FormTrait;
        
    public function navbar() 
    {
        $this->content['navbar'][] = [
            "list" => [ 
                "/admin/advertising" => _("View all"), 
                "/admin/advertising/new" => _("Add new advertising"), 
                "/admin/advertising/payment" => ucfirst(_("payments")),
                "/admin/advertising/expired" => ucfirst(_("expired contracts")) 
            ],
            "attributes" => [ "class" => "menu menu3" ],
            "title" => _(ucfirst("advertising"))            
        ];
    }
    
    public function index(array $data): array
    {        
        $this->navbar();
        
        $this->content['main'][] = [ "tag" => "h4", "content" => _("Advertisings") ]; 
        $this->content['main'][] = self::search("", "search", filter_input(INPUT_GET, 'search'));
        
        $this->content['main'][] = [ "tag" => "p", "content" => sprintf("Mostrando %s contratos não vencidos e por ordem descendente de vencimento", $data['numberOfItems']) ];
        
        // table head
        $content[] = [ "tag" => "thead", "content" => [
            [ "tag" => "tr", "content" => [
                [ "tag" => "th", "attributes" => [ "style" => "width: 10px;" ], "content" => "#" ],
                [ "tag" => "th", "attributes" => [ "style" => "width: 10px;" ], "content" => "Id" ],
                [ "tag" => "th", "attributes" => [ "style" => "width: 80px;" ], "content" => _("Date") ],
                [ "tag" => "th", "content" => _("Customer") ],
                [ "tag" => "th", "attributes" => [ "style" => "width: 170px;" ], "content" => _("Type") ],
                [ "tag" => "th", "attributes" => [ "style" => "width: 100px;" ], "content" => _("Expired date") ],
                [ "tag" => "th", "attributes" => [ "style" => "width: 80px;" ],"content" => _("Value")." (R$)" ],
                [ "tag" => "th", "attributes" => [ "style" => "width: 15px;" ],"content" => "Status" ]
            ]]
        ] ];
        
        // table body
        if ($data['itemListElement']) {
            foreach ($data['itemListElement'] as $key => $value) {
                $item = $value['item'];
                
                $id = PropertyValue::extractValue($item['identifier'], "id");
                
                $body[] = [ "tag" => "tr", "attributes" => [ "style" => $item['status'] == 1 ? "opacity: 1;" : "opacity: 0.5;" ], "content" => [
                    [ "tag" => "td", "content" => $key+1 ],
                    [ "tag" => "td", "content" => $id ],
                    [ "tag" => "td", "content" => DateTime::formatDate($item['data']) ],
                    [ "tag" => "td", "content" => $item['customer']['name'] ],
                    [ "tag" => "td", "content" => "<a href=\"/admin/advertising/edit/$id\">". self::contractTypeNumberToString($item['tipo'])."</a>" ],
                    [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => DateTime::formatDate($item['vencimento']) ],
                    [ "tag" => "td", "attributes" => [ "style" => "text-align: right;" ], "content" => number_format($item['valor'],2,",",".") ],
                    [ "tag" => "td", "content" => $item['status'] ]
                ]];
            }
            
        } else {
            $body[] = [ "tag" => "tr", "content" => 
                [ "tag" => "td", "attributes" => [ "colspan" => "8", "style" => "font-size: 130%; text-align: center; font-weight: bold;" ], "content" => "Nada encontrado!" ]
            ];
        }
        
        $content[] = [ "tag" => "tbody", "content" => $body ];
        
        $this->content['main'][] = [ "tag" => "table", "attributes" => [ "class" => "table" ], "content" => $content ];
        
        return $this->content;
    }        
    
    public function new($data = null): array
    {
        $this->navbar();

        $this->content['main'][] = [ "tag" => "h4", "content" => _("Add contract") ];        
        // contract
        $this->content['main'][] = self::form("new", $data);
        
        return $this->content;
    }
    
    public function edit(array $data): array
    {        
        $advertising = $data['advertising'];        
        $customer = $advertising['customer'];        
        $payment = $data['payment'];
        $banner = $data['banner'] ?? null;
        
        $this->navbar();
        
        $idLocalBusiness = PropertyValue::extractValue($customer['identifier'], "id");
        
        $this->content['main'][] = [ "tag" => "h4", "content" => _("Editing contract") ];
        $this->content['main'][] = [ "tag" => "p", "content" => _("View ad"), "href" => "/". str_replace(" ", "", $customer['name'])."/".$idLocalBusiness, "hrefAttributes" => [ "target" => "_blank" ] ];
        
        // advertising
        $this->content['main'][] = self::form("edit", $advertising);
        
        // payments
        $this->content['main'][] = (new PaymentView())->edit($data);
        
        // history
        $this->content['main'][] = (new HistoryView())->view($data['history']);
               
        // banner
        $this->content['main'][] = $banner ? (new BannerView())->getBannerByIdcontrato($banner) : null;
        
        return $this->content;
    }
    
    static private function form($case = "new", $value = null) 
    {
        
        $content[] = [ "tag" => "h3", "content" => $value['customer']['name'] ?? _("New advertising") ];
        
        if ($case == "edit") {        
            $idcustomer = PropertyValue::extractValue($value['customer']['identifier'], "id");
            $idadvertising = PropertyValue::extractValue($value['identifier'], "id");
            
            $content[] = [ "tag" => "p", "content" => _("Edit Local Business"), "href" => "/admin/localBusiness/edit/".$idcustomer ];
            
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $idadvertising ]];
            $tipos[] = [ "tag" => "option", "attributes" => [ "value" => $value['tipo'] ], "content" => self::contractTypeNumberToString($value['tipo']) ];
            
        } elseif ($case = "new") {
            $localBusiness[] = [ "tag" => "option", "attributes" => [ "value" => "0" ], "content" => _("Choose a local business...") ];
            
            foreach ($value as $valueLB) {            
                $localBusiness[] = [ "tag" => "option", "attributes" => [ "value" => \Plinct\Api\Type\PropertyValue::extractValue($valueLB['identifier'], "id") ], "content" => $valueLB['name'] ];
            } 
            
            $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: auto;" ], "content" => [
                [ "tag" => "legend", "content" => _("Local business") ],
                [ "tag" => "select", "attributes" => [ "name" => "customer"], "content" => $localBusiness ]
            ]];
        }
        
        // contract types 
        $tipos[] = [ "tag" => "option", "attributes" => [ "value" => "0" ], "content" => "Escolha um tipo..." ];   
        
        foreach (self::$ContractTypes as $key => $valueTypes) {            
            $tipos[] = [ "tag" => "option", "attributes" => [ "value" => $key ], "content" => $valueTypes ];
        }   
        
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: auto;" ], "content" => [
            [ "tag" => "legend", "content" => _("Contract type") ],
            [ "tag" => "select", "attributes" => [ "name" => "tipo"], "content" => $tipos ]
        ]];
        
        // date
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: auto;" ], "content" => [
            [ "tag" => "legend", "content" => _("Date") ],
            [ "tag" => "input", "attributes" => [ "name" => "data", "type" => "date", "value" => $value['data'] ?? null ] ]
         ]];
        
        // valor
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: auto;" ], "content" => [
            [ "tag" => "legend", "content" => _("Amount") ],
            [ "tag" => "input", "attributes" => [ "name" => "valor", "type" => "number", "value" => $value['valor'] ?? null ] ]
         ]];
        
        // date
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: auto;" ], "content" => [
            [ "tag" => "legend", "content" => _("Expired date") ],
            [ "tag" => "input", "attributes" => [ "name" => "vencimento", "type" => "date", "value" => $value['vencimento'] ?? null ] ]
         ]];
        
        // status
        $valuesStatus = [ 0 => "Inativo", 1 => "Ativo", 2 => "Suspenso" ];
        if ($case == "edit") {
            $statusValue[] = [ "tag" => "option", "attributes" => [ "value" => $value['status'] ], "content" => ucfirst($valuesStatus[$value['status']]) ];
        }
        $statusValue[] = [ "tag" => "option", "attributes" => [ "value" => 0 ], "content" => _("Choose...") ];
        foreach ($valuesStatus as $key => $valueOption) {
            $statusValue[] = [ "tag" => "option", "attributes" => [ "value" => $key ], "content" => $valueOption ];
        }
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: auto;" ], "content" => [
            [ "tag" => "legend", "content" => "Status" ],
            [ "tag" => "select", "attributes" => [ "name" => "status"], "content" => $statusValue ]
        ]];
        
        // tags
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 70%;" ], "content" => [
            [ "tag" => "legend", "content" => _("Tags") ],
            [ "tag" => "input", "attributes" => [ "name" => "tags", "type" => "text", "value" => $value['tags'] ?? null ] ]
         ]];
        
        $content[] = $case == "edit" ? self::submitButtonSend([ "onclick" => "return setHistory(this.parentNode);" ]) : self::submitButtonSend();
        
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/advertising/erase") : null;
        
        return [ "tag" => "form", "attributes" => [ "id" => "contract-form", "class" => "box formPadrao", "action" => "/admin/advertising/$case", "method" => "post" ], "content" => $content ];
    }
    
    public function payment($data)
    {
        $this->navbar();
        
        $content[] = [ "tag" => "h3", "content" => "Overdue payments" ];        
        $content[] = self::selectPeriodo($data['numberOfItems'], "payment");        
        
        $total = 0;
        foreach ($data['itemListElement'] as $key => $value) {
            $tbody[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => DateTime::formatDate($value['vencimentoparc']) ],
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => number_format($value['valorparc'],2,",",".") ],
                [ "tag" => "td", "content" => $value['name']." <a href=\"/admin/advertising/edit/".$value['idadvertising']."\">-></a>" ],
                [ "tag" => "td", "content" => $value['parcela']." / ".$value['number_parc'] ],
                [ "tag" => "td", "content" => "<a href=\"/admin/advertising/edit/".$value['idadvertising']."\">".$value['contrato_name']."</a>" ]
            ]];
            $total += $value['valorparc'];
        }
        // total
        $tbody[] = [ "tag" => "tr", "attributes" => [ "style" => "background-color: rgba(0,0,0,0.65);" ], "content" => [
            [ "tag" => "td", "attributes" => [ "style" => "text-align: center"], "content" => "TOTAL" ],
            [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => "R$ ".number_format($total,2,",",".") ],
            [ "tag" => "td", "attributes" => [ "style" => "text-align: center"], "content" => ($key+1). " itens" ],
            [ "tag" => "td", "content" => "" ],
            [ "tag" => "td", "content" => "" ]
        ]];
        
        $content[] = [ "tag" => "table", "attributes" => [ "class" => "table" ], "content" => [
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "th", "attributes" => [ "style" => "width: 100px;" ], "content" => "Vencimento" ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 100px;" ], "content" => "Valor (R$)" ],
                    [ "tag" => "th", "content" => "Local Business" ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 80px;" ], "content" => "Parcela" ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 180px;" ], "content" => "Contract type" ]
                ]]
            ]],
            [ "tag" => "tbody", "content" => $tbody ]
        ] ];
        
        $content[] = [ "tag" => "p", "content" => "Imprimir", "href" => "javascript: void(0);", "hrefAttributes" => [ "onclick" => "print();" ] ];
        
        $this->content['main'][] = [ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ];
        
        return $this->content;
    }
    
    public function expired($data) 
    {
        $this->navbar();
        
        $content[] = [ "tag" => "h3", "content" => "Expired contracts" ];        
        $content[] = self::selectPeriodo($data['numberOfItems'], "expired");
        
        foreach ($data['itemListElement'] as $key => $value) {
            $tbody[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => DateTime::formatDate($value['vencimento']) ],
                [ "tag" => "td", "content" => $value['name'] ],
                [ "tag" => "td", "content" => "<a href=\"/admin/advertising/edit/".$value['idadvertising']."\">".$value['contrato_name']."</a>" ]
            ]];
        }   
        
        $content[] = [ "tag" => "table", "attributes" => [ "class" => "table" ], "content" => [
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "th", "attributes" => [ "style" => "width: 100px;" ], "content" => "Vencimento" ],
                    [ "tag" => "th", "content" => "Local Business" ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 180px;" ], "content" => "Contract type" ]
                ]]
            ]],
            [ "tag" => "tbody", "content" => $tbody ]
        ] ];
        
        $content[] = [ "tag" => "p", "content" => "Imprimir", "href" => "javascript: void(0);", "hrefAttributes" => [ "onclick" => "print();" ] ];        
        $this->content['main'][] = [ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ];
        
        return $this->content;
    }
    
    static private function selectPeriodo($numberOfItens, $section) 
    {
        $content[] = [ "tag" => "form", "attributes" => [ "class" => "noprint", "action" => "/admin/advertising/$section", "method" => "get" ], "content" => [
            [ "tag" => "select", "attributes" => [ "onchange" => "submit();", "name" => "period" ], "content" => [
                [ "tag" => "option", "attributes" => [ "value" => "" ], "content" => "Selecionar por período" ],
                [ "tag" => "option", "attributes" => [ "value" => "past" ], "content" => "Até hoje" ],
                [ "tag" => "option", "attributes" => [ "value" => "current_month" ], "content" => "Até o fim do mês corrente" ],
                [ "tag" => "option", "attributes" => [ "value" => "all" ], "content" => "Todos" ]
            ] ]
        ] ]; 
        switch (filter_input(INPUT_GET, 'period')) {
            case "current_month":
                $period = "até o mês corrente - <b>".DateTime::translateMonth(date('m'))." ".date('Y')."</b>";
                break;
            case "past":
                $period = "até hoje - <b>".DateTime::formatDate();
                break;
            default :
                $period = null;
                break;
        }
        $content[] = [ "tag" => "p", "content" => "Showing ".$numberOfItens." itens $period" ];     
        
        return [ "tag" => "div", "content" => $content ];
    }
    
    private static function contractTypeNumberToString($number): string 
    {
        switch ($number) {
            case 1:
                return "Hospedagem de Domínio";
                
            case 2:
                return "Inserção com Vínculo";
                
            case 3:
                return "Subdomínio";
                
            case 4:
                return "Banner";
                
            case 5:
                return "Inserção sem Vínculo";

            default:
                return "Tipo $number - Não definido";
        }
    }
}
