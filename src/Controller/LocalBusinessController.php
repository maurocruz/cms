<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\DateTime;
use Plinct\Tool\Image\Image;
use Plinct\Tool\Sitemap;

class LocalBusinessController implements ControllerInterface
{
    public function index($params = null): array {
        //$idOrganization = "52";
        /*$dataLocalBusiness = Api::get("localBusiness", [ "properties" => "*,contactPoint,image,member", "limit" => "0,10" ]);
        foreach ($dataLocalBusiness as $valueOrganization) {
            $contactPoint = $valueOrganization['contactPoint'];
            $image = $valueOrganization['image'];
            $member = $valueOrganization['member'];
            // POST ORGANIZATION
            unset($valueOrganization['@context']);
            unset($valueOrganization['@type']);
            unset($valueOrganization['organization']);
            unset($valueOrganization['idlocalBusiness']);
            unset($valueOrganization['contactPoint']);
            unset($valueOrganization['image']);
            unset($valueOrganization['member']);
            unset($valueOrganization['identifier']);
            $resp = Api::post('organization', $valueOrganization);
            $idOrganization = $resp['id'];
            var_dump($resp);
            // POST CONTACT POINT
            if ($contactPoint) {
                foreach ($contactPoint as $valueContactPoint) {
                    unset($valueContactPoint['@context']);
                    unset($valueContactPoint['@type']);
                    unset($valueContactPoint['idcontactPoint']);
                    unset($valueContactPoint['idlocalBusiness']);
                    unset($valueContactPoint['identifier']);
                    $valueContactPoint['tableHasPart'] = "organization";
                    $valueContactPoint['idHasPart'] = $idOrganization;
                    $respC = Api::post('contactPoint', $valueContactPoint);
                    var_dump($respC);
                }
            }
            // POST IMAGE
            if ($image) {
                foreach ($image as $valueImage) {
                    unset($valueImage['@context']);
                    unset($valueImage['@type']);
                    unset($valueImage['idimageObject']);
                    unset($valueImage['idlocalBusiness']);
                    unset($valueImage['identifier']);
                    unset($valueImage['caption']);
                    unset($valueImage['representativeOfPage']);
                    unset($valueImage['position']);
                    $valueImage['tableHasPart'] = "organization";
                    $valueImage['idHasPart'] = $idOrganization;
                    $valueImage['contentUrl'] = "https://pirenopolis.tur.br" . $valueImage['contentUrl'];
                    $imageObject = new Image($valueImage['contentUrl']);
                    $valueImage['width'] = $imageObject->getWidth();
                    $valueImage['height'] = $imageObject->getHeight();
                    $valueImage['encodingFormat'] = $imageObject->getEncodingFormat();
                    $respI = Api::post('imageObject', $valueImage);
                    var_dump($respI);
                }
            }
            // POST PERSON
            if ($member) {
                foreach ($member as $valueMember) {
                    unset($valueMember['@context']);
                    unset($valueMember['@type']);
                    unset($valueMember['identifier']);
                    unset($valueMember['idperson']);
                    unset($valueMember['idlocalBusiness']);
                    unset($valueMember['jobTitle']);
                    unset($valueMember['position']);
                    $valueMember['tableHasPart'] = "organization";
                    $valueMember['idHasPart'] = $idOrganization;
                    $respP = Api::post('person', $valueMember);
                    var_dump($respP);
                }
            }
        }
        die();*/

        $params2 = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "additionalType,dateModified" ];
        $params3 = $params ? array_merge($params2, $params) : $params2;
        return Api::get("localBusiness",$params3);
    }
    
    public function edit(array $params): array {
        $newParams = array_merge($params, [ "properties" => "*,location,address,organization,contactPoint,member,image" ]);
        return Api::get("localBusiness",$newParams);
    }
    
    public function new($params = null): bool {
        return true;
    }

    public function saveSitemap($params = null) {
        $dataSitemap = null;
        $data = Api::get("localBusiness",[ "orderBy" => "dateModified desc", "properties" => "image,dateModified" ]);
        foreach ($data as $value) {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $dataSitemap[] = [
                "loc" => App::$HOST . "/t/localBusiness/$id",
                "lastmod" => DateTime::formatISO8601($value['dateModified']),
                "image" => $value['image']
            ];
        }
        (new Sitemap("sitemap-localBusiness.xml"))->saveSitemap($dataSitemap);
    }
}
