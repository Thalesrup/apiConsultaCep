<?php

namespace App\Services;
use GuzzleHttp;
use App\Http\Controllers\ApiController;

class BuscaCepLogadouro extends ApiController
{
    protected $client;
    public $clauseApply = false;

    public function __construct()
    {
        $this->client = new GuzzleHttp\Client;
    }

    public function setClauseApply() 
    {
        $this->clauseApply = true;
        return $this;
    }

    public function montaRequisicao($parametro)
    {
        try {
            $response = $this->client->request('POST', env('URL_CONSULTA_CORREIOS'), [
                'form_params' => [
                    'pagina' => '/app/endereco/index.php',
                    env('CONSULTA_POR_CEP') => $parametro,
                    'tipoCEP' => 'ALL',
                ] + self::$clause
            ]);

            $body = $response->getBody()->getContents();
            return json_decode($body, true);

        } catch (GuzzleHttp\Exception\TooManyRedirectsException $e) {
            return (object)[
                'success' => false,
                'message' =>'Serviço Indisponivel no Momento',
                'status' => $e->getCode(),
            ];
        }

    }

    public function buscar($parametro)
    {
        $jsonResponse = $this->montaRequisicao($parametro);

        if(isset($jsonResponse['erro']) && $jsonResponse['erro']) {
            return (object) [
                'success' => false, 
                'message' =>'Serviço Indisponivel no Momento', 
                'status' => '429'
            ];
        }

        return (self::getTypeOutout() == 'xml')
            ? $this->arrayToXml($this->mockReturn($jsonResponse))
            : $this->mockReturn($jsonResponse);
    }

    public function mockReturn($array): array
    {
        return [
            'enderecos' => $array['dados'],
            'total' => sprintf(
                "total %s de %d",
                (string) count($array['dados']), 
                $array['total'],
            ),
            'allowedOffset' => $array['total'],
        ];
    }

    public function arrayToXml($array, $xmlElement = null) 
    {
        if (!$xmlElement) {
            $xmlElement = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>');
            $xmlElement->addChild('title', 'lista Enderecos');
            $group = $xmlElement->addChild('enderecos');
            $xmlElement->addChild('totalRegistros', $array['total']);
        }
        
        if (isset($array['enderecos'])) {
            foreach ($array['enderecos'] as $element) {
                if (is_array($element)) {
                    $subElement = $group->addChild('endereco');
                    foreach($element as $key => $value) {
                        (is_array($value))
                            ? $this->arrayToXml($value, $subElement)
                            : $subElement->addChild(preg_replace("/[^A-Za-z0-9]/", "",$key), $value);
                    }
                }
            }
        }
        
        return $xmlElement->asXML();
    }

}
