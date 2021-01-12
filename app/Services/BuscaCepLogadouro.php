<?php

namespace App\Services;
use GuzzleHttp;
use DOMDocument;
use App\Http\Controllers\ApiController;

class BuscaCepLogadouro extends ApiController
{
    protected $client;

    public function __construct()
    {
        $this->client = new GuzzleHttp\Client;
    }

    public function montaRequisicao($parametro)
    {

        try {
            $response = $this->client->request('POST', env('URL_CONSULTA_CORREIOS'), [
                'form_params' => [
                    env('CONSULTA_POR_CEP') => $parametro,
                    'tipoCEP'    => 'ALL',
                    'semelhante' => 'N'
                ]
            ]);

            $body = $response->getBody(true);

            return $body;

        } catch (GuzzleHttp\Exception\TooManyRedirectsException $e) {
            return (object)['success' => false, 'message' =>'Serviço Indisponivel no Momento', 'status' => $e->getCode()];
        }

    }

    public function buscar($parametro)
    {
        $htmlResponse = $this->montaRequisicao($parametro);

        if(isset($htmlResponse->success)){
            return $htmlResponse;
        }

        libxml_use_internal_errors(true);
        $DOM   = new DOMDocument();
        $DOM->preserveWhiteSpace = false;
        $DOM->loadHTML($htmlResponse);

        $trOrTableList = $DOM->getElementsByTagName("table");

        if($trOrTableList->length <= 0){
            return (object)['success' => false, 'message' =>'Informe um CEP ou Logadouro Válido', 'status' => 400];
        }

        foreach ($trOrTableList as $tr)  {
            $row = [];
                foreach ($tr->getElementsByTagName("td") as $td)  {
                    $row[] = trim($td->textContent);
            }

            $rows[] = $row;
            $chavesCallback = explode(':', trim(str_replace('/', '', $tr->getElementsByTagName("tr")[0]->nodeValue)));
            array_pop($chavesCallback);
            $chavesCallback = $this->validaChavesJsonRetorno($chavesCallback);
        }

        $validarRetorno = array_chunk($rows[0],4);
        foreach($validarRetorno as $chave => $dados){
           $arrayResultado[] = array_combine($chavesCallback, $dados);
        }

        return (self::getTypeOutout() == 'xml') ? $arrayResultado  : ['enderecos' => $arrayResultado];

    }

    public function validaChavesJsonRetorno($arrayChaves)
    {
        if(count($arrayChaves) == 4){
           return $arrayChaves;
        } else {
            return ['LogradouroNome', 'BairroDistrito', 'LocalidadeUF', 'CEP'];
        }
    }


}
