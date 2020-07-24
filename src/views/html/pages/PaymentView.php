<?php

namespace Plinct\Cms\View\Html\Page;

class PaymentView
{      
    public function edit(array $data): array
    {
        $idadvertising = \Plinct\Api\Type\PropertyValue::extractValue($data['advertising']['identifier'], "id");
        
        $content[] = [ "tag" => "h4", "content" => "Payments" ];  
        
        $data_exists = isset($data['payment']['message']) && $data['payment']['message'] == "No data founded" ? false : true;
        
        // new
        $content[] = self::form($idadvertising, "new", null, $data_exists ? (count($data['payment'])+1) : 1);
        
        if ($data_exists) {
            foreach ($data['payment'] as $value) {
                $content[] = self::form($idadvertising, 'edit', $value);
            }

            // balance
            $content[] = self::balance($data['payment']);  
        }
        
        return [ "tag" => "div", "attributes" => [ "id" => "account-form", "class" => "box" ], "content" => $content ];
    }
    
    private static function form($idadvertising, $case = 'new', $value = null, $n = null) 
    {
        $idpayment = $value ? \Plinct\Api\Type\PropertyValue::extractValue($value['identifier'], "id") : null;
        
        // hiddens
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idadvertising", "value" => $idadvertising, "type" => "hidden"] ];        
        
        if ($case == "edit") {
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "idpayment", "value" => $idpayment, "type" => "hidden"] ];
        }   
        
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "output", "value" => "referer", "type" => "hidden"] ];
            
        // parcela
        $p = $case == "new" ? $n+1 : $value['parcela'];
        $content[] = [ "tag" => "fieldset", "content" => [ 
            $case == "new" ? [ "tag" => "legend", "content" => "Parcela" ] : null,
            [ "tag" => "input", "attributes" => [ "name" => "parcela", "value" => $p, "type" => "text" ] ]
        ]];        
        // valor
        $content[] = [ "tag" => "fieldset", "content" => [ 
            $case == "new" ? [ "tag" => "legend", "content" => "Valor" ] : null,
            [ "tag" => "input", "attributes" => [ "name" => "valorparc", "value" => $value['valorparc'], "type" => "number", "step" => "0.01", "min" => "0.01", "style" => "text-align: right;" ]]
        ]];
        // Vencimento
        $content[] = [ "tag" => "fieldset", "content" => [ 
            $case == "new" ? [ "tag" => "legend", "content" => "Vencimento" ] : null,
            [ "tag" => "input", "attributes" => [ "name" => "vencimentoparc", "value" => $value['vencimentoparc'], "type" => "date" ]]
        ]];
        // Quitado em
        $content[] = [ "tag" => "fieldset", "content" => [ 
            $case == "new" ? [ "tag" => "legend", "content" => "Quitado em" ] : null,
            [ "tag" => "input", "attributes" => [ "name" => "quitado", "value" => $value['quitado'], "type" => "date" ]]
        ]];
                
        if ($case == "new") {
            $content[] = [ "tag" => "fieldset", "content" => [ 
                [ "tag" => "input", "attributes" => [ "name" => "submit", "value" => "Adicionar novo", "class" => "form-button-submit", "type" => "submit" ]]
            ]];
        } elseif ($case == "edit" ) {
            $content[] = [ "tag" => "fieldset", "content" => [ 
                [ "tag" => "input", "attributes" => [ "name" => "submit", "value" => "Atualizar", "class" => "form-button-submit", "onclick" => "this.value = 'Aguarde...'", "type" => "submit" ]]
            ]];
            $content[] = [ "tag" => "fieldset", "content" => [ 
                [ "tag" => "input", "attributes" => [ "name" => "submit", "value" => "Eliminar", "class" => "form-button-submit", "onclick" => "return confirm('Tem certeza que deseja excluir esta parcela?'); this.value = 'Aguarde...'", "formaction" => "/admin/payment/erase", "type" => "submit" ]]
            ]];
        }
        
        return [ "tag" => "form", "attributes" => [ "id" => "form-payments-".$idpayment, "name" => "form-payments", "action" => "/admin/payment/".$case, "method" => "post", "class" => "form-table ".self::classStyle($value), "onSubmit" => "return CheckRequiredFieldsInForm(event,['valorparc','vencimentoparc']);" ], "content" => $content ];
    }
    
    static private function classStyle($value) 
    {
        $now = new \DateTime();
        $expired = new \DateTime($value['vencimentoparc']); 
        $diff = $expired->diff($now);
                
        if ($value == null) { 
            return "form-back-gray"; 
            
        } elseif ($value['quitado'] && $value['quitado'] !== "0000-00-00") {
            return "form-back-green";
            
        } elseif($diff->invert == 0) {
            return "form-back-red";
            
        } elseif($diff->days < 30) {
            return "form-back-yellow";
            
        } else { 
            return "form-back-white";             
        }
    }


    private static function balance($data) {        
        $dadosSaldo = self::saldoData($data);        
        return [ "tag" => "table", "content" => [
            [ "tag" => "h5", "content" => "Saldo" ],
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "th", "content" => "Total das parcelas"],
                    [ "tag" => "th", "content" => "Valor pago"],
                    [ "tag" => "th", "content" => "Valor a receber"],
                    [ "tag" => "th", "content" => "Valor em débito"]
                ]]
            ]],
            [ "tag" => "tbody", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "td", "content" => "R$ ".number_format($dadosSaldo['total'],2,",",".") ],
                    [ "tag" => "td", "content" => "RS ".number_format($dadosSaldo['credito'],2,",",".") ],
                    [ "tag" => "td", "content" => "RS ".number_format($dadosSaldo['debito'],2,",",".")],
                    [ "tag" => "td", "content" => "RS ".number_format($dadosSaldo['atrasado'],2,",",".")]
                ]]
            ]]
        ]];
    }
    
    // SALDO
    private static function saldoData($data) {        
        $dadosSaldo = [];
        $dadosSaldo['total'] = 0;
        $dadosSaldo['credito'] = 0;
        $dadosSaldo['debito'] = 0;
        $dadosSaldo['atrasado'] = 0;
        
        foreach ($data as $value) {
            // total
            $dadosSaldo['total'] += $value['valorparc'];
            // pago            
            $dadosSaldo['credito'] += $value['quitado'] ? $value['valorparc'] : 0; 
            // debito
            $dadosSaldo['debito'] += $value['quitado'] ? null : $value['valorparc'];
            // atrasado
            $dadosSaldo['atrasado'] += $value['quitado'] == null && $value['vencimentoparc'] < date("Y-m-d") ? $value['valorparc'] : null;
        }
        
        return $dadosSaldo;
    }
}
