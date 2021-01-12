<?php

namespace App\Http\Controllers;

use App\Services\BuscaCepLogadouro;
use Illuminate\Http\Request;

class BuscaCepLogadouroController extends ApiController
{
    private $requisicao;

    public function getCep(Request $request)
    {

        if(!$this->validaCEP($request->cep)){
            return $this->sendResponse(
                false,
                'Erro Ao Validar Cep, Digite um Numero Válido Exemplo(92425553 ou 92425-553)',
                false,
                400);
        }

        $this->requisicao = new BuscaCepLogadouro();
        self::setTypeOutput(filter_input(INPUT_SERVER, 'QUERY_STRING'));

        $responseCorreios = $this->requisicao->buscar($request->cep);

        return (self::getTypeOutout() == 'xml') ? $this->setSaida($responseCorreios) :
         $this->sendResponse(
            true,
            'Requisição Bem Sucedida',
            $responseCorreios,
            200);

    }

    public function getLogadouro(Request $request)
    {
        if(!is_string($request->logadouro)){
            return $this->sendResponse(
                false,
                'Erro Ao Validar Logadouro, Digite uma string Válida Exemplo(av brasil ou avbrasil)',
                false,
                400);
        }

        $this->requisicao = new BuscaCepLogadouro();
        self::setTypeOutput(filter_input(INPUT_SERVER, 'QUERY_STRING'));

        $responseCorreios = $this->requisicao->buscar($this->validaString($request->logadouro));

        if(isset($responseCorreios->success)){
            return $this->sendResponse(
                $responseCorreios->success,
                $responseCorreios->message,
                false,
                $responseCorreios->status);
        }

        return (self::getTypeOutout() == 'xml') ? $this->setSaida($responseCorreios) :
         $this->sendResponse(
        true,
        'Requisição Bem Sucedida',
        $responseCorreios,
        200);

    }

    public function validaCEP($cep)
    {
        $valida = preg_match('/^[0-9]{5,5}([- ]?[0-9]{3,3})?$/', str_replace('-', '', $cep));
        if($valida <= 0){
            return false;
        }

        return $valida;
    }

    public function setSaida($saidaCustom)
    {
        $returnXmlDocument = $this->mountResponseToXML($saidaCustom,'enderecos', 'endereco', 'lista Enderecos');
        return response($returnXmlDocument)->header('Content-type', 'text/xml');
    }

    public function validaString($string){
        $string    = iconv( "UTF-8" , "ASCII//TRANSLIT//IGNORE" , $string);
        $string    = str_replace(" "," ",preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($string))));

        $especiais = [".",",",";","!","@","#","%","¨","*","(",")","+","-","=", "§","$","|","\\",":","/","<",">","?","{","}","[","]","&","'",'"',"´","`","?",'“','”','$',"'","'"];
        $string    = str_replace($especiais,"",trim($string));

        return $string;
    }

}
