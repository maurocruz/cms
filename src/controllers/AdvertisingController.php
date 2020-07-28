<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Server\PDOConnect;
use Plinct\Api\Type\PropertyValue;
use Plinct\Api\Type\LocalBusiness;
use Plinct\Api\Type\Advertising;
use Plinct\Api\Type\Payment;
use Plinct\Api\Type\History;
use Plinct\Api\Type\Banner;

class AdvertisingController 
{
    public function index($params) 
    {
        $search = $params['search'] ?? null;
        
        if ($search) {
            $query = "SELECT * FROM advertising,localBusiness WHERE localBusiness.name LIKE '%$search%' AND localBusiness.idlocalBusiness=advertising.customer ORDER BY vencimento DESC, status DESC;";
            
            $data = PDOConnect::run($query);
            
            $response['numberOfItems'] = count($data);
            
            foreach ($data as $value) {
                //var_dump($value);
                $item['item']['identifier'][] = [ "value" => $value['idadvertising'], "name" => "id" ]; 
                $item['item']['customer']['name'] = $value['name']; 
                $item['item']['status'] = $value['status']; 
                $item['item']['data'] = $value['data']; 
                $item['item']['tipo'] = $value['tipo']; 
                $item['item']['vencimento'] = $value['vencimento']; 
                $item['item']['valor'] = $value['valor']; 
                $response['itemListElement'][] = $item;
                unset($item);
            }
            
            return $response;
            
        } else {        

            $requiredParams = [ "format" => "ItemList", "where" => "vencimento >=CURDATE()", "orderBy" => "vencimento" ];

            $finalParams = array_merge($requiredParams, $params);

            $data = (new Advertising())->get($finalParams);
        }
        
        return $data;
    }


    public function edit($params): array 
    {
        // advertising
        $params["properties"] = "tags,history";        
        $adverisingData = (new Advertising())->get($params);        
        $response['advertising'] = $adverisingData[0];
        $idadvertising = PropertyValue::extractValue($adverisingData[0]['identifier'], 'id');
        
        // payments
        $paramsPayment = [ "where" => "`idadvertising`=$idadvertising", "orderBy" => "vencimentoparc", "ordering" => "desc" ];
        $paymentData = (new Payment())->get($paramsPayment);        
        $response['payment'] = $paymentData;
        
        // history
        $paramsHistory = [ "tableHasPart" => "advertising", "idHasPart" => $idadvertising, "orderBy" => "datetime", "ordering" => "desc" ];
        $historyData = (new History())->get($paramsHistory);
        $response['history'] = $historyData;
        
        // banner
        if ($adverisingData[0]['tipo'] == '4') {
            $paramsBanner = [ "where" => "`idadvertising`=$idadvertising" ];
            $bannerData = (new Banner())->get($paramsBanner);
            $response['banner'] = $bannerData[0] ?? null;
        }
        
        return $response;
    }
    
    public function new() 
    {
        return (new LocalBusiness())->get([ "limit" => "none", "orderBy" => "name" ]);
    }
    
    public function payment()
    {
        $date = self::translatePeriod(filter_input(INPUT_GET, 'period'));
        
        $query = "SELECT *, (SELECT COUNT(*) FROM payment WHERE payment.idadvertising=advertising.idadvertising) as number_parc FROM payment, advertising, localBusiness, contratostipos WHERE payment.quitado is null AND payment.idadvertising=advertising.idadvertising and advertising.status=1 AND advertising.customer=localBusiness.idlocalBusiness AND advertising.tipo=contratostipos.idcontratostipo";
        $query .= $date ? " AND payment.vencimentoparc <= '$date'" : null;
        $query .= " ORDER BY vencimentoparc ASC";
        $query .= ";";
        
        $data = PDOConnect::run($query);
        
        $return = [
            "@type" => "ItemList",
            "numberOfItems" => count($data),
            "itemListElement" => $data
        ]; 
        return $return;
        
        //return (new \Plinct\Api\Type\Payment())->get([ "format" => "ItemList" ]);
    }
    
    public function expired() 
    {           
        $dateLimit = self::translatePeriod(filter_input(INPUT_GET, 'period'));
            
        $query = "SELECT advertising.*, localBusiness.name, contratostipos.contrato_name FROM advertising, contratostipos, localBusiness WHERE advertising.status=1 AND contratostipos.idcontratostipo=advertising.tipo AND advertising.customer=localBusiness.idlocalBusiness";
        $query .= $dateLimit ? " AND advertising.vencimento < '$dateLimit'" : null;
        $query .= " ORDER BY vencimento ASC";
        $query .= ";";
        
        $data = PDOConnect::run($query);    
        
        $return = [
            "@type" => "ItemList",
            "numberOfItems" => count($data),
            "itemListElement" => $data
        ]; 
        
        return $return;
    }
    
    static private function translatePeriod($get) 
    {
        switch ($get) {
            case "past":
                return date("Y-m-d");
                
            case "current_month":
                return date('Y-m-t');
                
            case "all":                
                return null;
        }
    }
}
