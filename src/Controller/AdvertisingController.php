<?php
namespace Plinct\Cms\Controller;

use Plinct\PDO\PDOConnect;
use Plinct\Api\Type\LocalBusiness;
use Plinct\Api\Type\Order;

class AdvertisingController 
{
    public function index($params): array {
        $search = $params['search'] ?? null;
        if ($search) {
            $query = "SELECT * FROM `order`,localBusiness WHERE localBusiness.name LIKE '%$search%' AND localBusiness.idlocalBusiness=`order`.customer ORDER BY paymentDueDate DESC, orderStatus DESC;";
            $data = PDOConnect::run($query);
            $response['numberOfItems'] = count($data);
            foreach ($data as $value) {
                $item['item']['identifier'][] = [ "value" => $value['idorder'], "name" => "id" ];
                $item['item']['customer']['name'] = $value['name']; 
                $item['item']['orderStatus'] = $value['orderStatus'];
                $item['item']['orderDate'] = $value['orderDate'];
                $item['item']['tipo'] = $value['tipo']; 
                $item['item']['paymentDueDate'] = $value['paymentDueDate'];
                $item['item']['valor'] = $value['valor']; 
                $response['itemListElement'][] = $item;
                unset($item);
            }
            return $response;
        } else {
            $requiredParams = [ "format" => "ItemList", "properties" => "*,customer", "where" => "paymentDueDate>=CURDATE()", "orderBy" => "paymentDueDate" ];
            $finalParams = array_merge($requiredParams, $params);
            $data = (new Order())->get($finalParams);
        }
        return $data;
    }

    public function edit($params): array {
        return (new OrderController())->edit($params);
    }
    
    public function new(): array {
        return (new LocalBusiness())->get([ "limit" => "none", "orderBy" => "name" ]);
    }
    
    public function payment(): array {
        $date = self::translatePeriod(filter_input(INPUT_GET, 'period'));
        $query = "SELECT *, (SELECT COUNT(*) FROM `invoice` WHERE `invoice`.referencesOrder=`order`.idorder) as number_parc FROM `invoice`, `order`, localBusiness, contratostipos WHERE (`invoice`.paymentDate = '0000-00-00' OR `invoice`.paymentDate is null) AND `invoice`.referencesOrder=`order`.idorder and `order`.orderStatus!='' AND `order`.customer=localBusiness.idlocalBusiness AND `order`.tipo=contratostipos.idcontratostipo";
        $query .= $date ? " AND `invoice`.paymentDueDate <= '$date'" : null;
        $query .= " ORDER BY `invoice`.paymentDueDate ASC;";
        $data = PDOConnect::run($query);
        return [
            "@type" => "ItemList",
            "numberOfItems" => count($data),
            "itemListElement" => $data
        ];
    }
    
    public function expired(): array {
        $dateLimit = self::translatePeriod(filter_input(INPUT_GET, 'period'));
        $query = "SELECT `order`.*, localBusiness.name, contratostipos.contrato_name FROM `order`, contratostipos, localBusiness WHERE `order`.orderStatus='orderProcessing' AND contratostipos.idcontratostipo=`order`.tipo AND `order`.customer=localBusiness.idlocalBusiness";
        $query .= $dateLimit ? " AND `order`.paymentDueDate < '$dateLimit'" : null;
        $query .= " ORDER BY `order`.paymentDueDate ASC;";
        $data = PDOConnect::run($query);
        return [
            "@type" => "ItemList",
            "numberOfItems" => count($data),
            "itemListElement" => $data
        ];
    }
    
    static private function translatePeriod($get) {
        switch ($get) {
            case "past":
                return date("Y-m-d");
            case "current_month":
                return date('Y-m-t');
            default:
                return null;
        }
    }
}
