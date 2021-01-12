<?php

namespace App\Http\Controllers;


class ApiController extends Controller
{

    static $tipoSaida;

    public function sendResponse($success = true, $message, $dataCallback = [], $codeHttp)
    {
        $response = [
            'success' => $success,
            'message' => $message,
            'data'    => $dataCallback,
        ];

        return response()->json($response, $codeHttp);
    }

    public function mountResponseToXML($arrayData, $itemPaiCustom = 'itens', $itemCustom = 'item', $itemTitle = 'xmlLista') {

        $xmlDoc   = new \DOMDocument("1.0","UTF-8");
        $root     = $xmlDoc->appendChild($xmlDoc->createElement("root"));
        $root->appendChild($xmlDoc->createElement("title",$itemTitle));
        $tabUsers = $root->appendChild($xmlDoc->createElement($itemPaiCustom));

        foreach($arrayData as $data){
            if(!empty($data)){
                $tabUser = $tabUsers->appendChild($xmlDoc->createElement($itemCustom));
                foreach($data as $key => $val){
                    $tabUser->appendChild($xmlDoc->createElement(preg_replace("/[^A-Za-z0-9]/", "",$key), $val));
                }
            }
        }

        $tabUser->formatOutput = TRUE;
        return $xmlDoc->saveXML();
    }

    static function getTypeOutout()
    {
        return self::$tipoSaida;
    }

    static function setTypeOutput($saida)
    {
        self::$tipoSaida = $saida;
    }

}
