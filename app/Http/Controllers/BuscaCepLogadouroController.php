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
        $params = $this->amountParams(1);

        if ($params instanceof \Illuminate\Http\JsonResponse) {
            return $params;
        }

        $responseCorreios = $this->requisicao->buscar($request->cep);

        return (self::getTypeOutout() == 'xml')
            ? $this->setSaida($responseCorreios)
            : $this->sendResponse(
                true,
                'Requisição Bem Sucedida',
                $responseCorreios,
                200,
            );

    }

    public function getLogadouro(Request $request)
    {
        if(!is_string($request->logadouro)) {
            return $this->sendResponse(
                false,
                'Erro Ao Validar Logadouro, Digite uma string Válida Exemplo(av brasil ou avbrasil)',
                false,
                400,
            );
        }

        $this->requisicao = new BuscaCepLogadouro();
        $allowedOffset = $this->getOffsetAllowed($request->logadouro);
        $params = $this->amountParams($allowedOffset);

        if ($params instanceof \Illuminate\Http\JsonResponse) {
            return $params;
        }

        $responseCorreios = $this->requisicao->buscar($this->sanitizeString($request->logadouro));

        return (self::getTypeOutout() == 'xml')
            ? $this->setSaida($responseCorreios)
            : $this->sendResponse(
                true,
                'Requisição Bem Sucedida',
                $responseCorreios,
                200
            );
    }

    public function getOffsetAllowed($parameter) 
    {
        $response = $this->requisicao->buscar($this->sanitizeString($parameter));
        if (isset($response['allowedOffset'])) {
            return $response['allowedOffset'];
        }
    }

    public function amountParams($allowedOffset)
    {
        $request = Request::capture();
        $queryStrings = $request->query();
     
        if (
            isset($queryStrings['output']) && 
            !array_search($queryStrings['output'], ['xml', 'json'])
        ) {
            self::setTypeOutput($queryStrings['output']);
        }
        
        if (
            isset($queryStrings['limit']) 
            && isset($queryStrings['offset'])
        ) {
           return $this->queryClause($queryStrings+['allowedOffset' => $allowedOffset]);
        }
        
    }

    public function queryClause($queryStrings)
    {
        $limit = isset($queryStrings['limit']) ? abs($queryStrings['limit']) : 0;
        $offset = isset($queryStrings['offset']) ? abs($queryStrings['offset']) : 0;
        
        if ($limit - $offset > 50) {
            $offset = $limit - 50;
        }

        if($limit > 50 || $queryStrings['allowedOffset'] < $offset) {
            return $this->sendResponse(
                false,
                "Necessário informar inervalos de até 50 registro, Exemplo offset=100&limit=50, e respeitando o offset de {$queryStrings['allowedOffset']}",
                false,
                400,
            );
        } else {
            self::setClauseQuery(['inicio' => $offset, 'final' => $limit + $offset -1]);
        }
        
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
        return response($saidaCustom)->header('Content-type', 'text/xml');
    }

    public function sanitizeString($string)
    {
        $string = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $string);
        $string = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($string)));

        $especials = [
            '.', ',', ';', '!', '@', '#', '%', '¨', '*', 
            '(', ')', '+', '-', '=', '§', '$', '|', '\\', 
            ':', '/', '<', '>', '?', '{', '}', '[', ']', 
            '&', "'", '"', '´', '`', '?', '“', '”', '$', 
            "'", "'"
        ];

        return str_replace($especials, '', trim($string));
    }

}
