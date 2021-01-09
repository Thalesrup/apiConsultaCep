<?php

namespace App\Services;
use GuzzleHttp\Client;
use DOMDocument;

class BuscaCepLogadouro
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function montaRequisicao($parametro)
    {
        $response = $this->client->request('POST', env('URL_CONSULTA_CORREIOS'), [
            'form_params' => [
                env('CONSULTA_POR_CEP') => $parametro
            ]
        ]);

        $body = $response->getBody(true);
        return $body;
    }

    public function buscar($parametro)
    {
        $htmlResponse = $this->montaRequisicao($parametro);

        libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $dom->preserveWhiteSpace = false;
        $dom->loadHTML($htmlResponse);
        $rows = $dom->getElementsByTagName('tr');

        if ($rows->length <= 1) {
            return [
                'error' => true,
                'message' => 'Nenhum endereço encontrado'
            ];
        }

        $header = [];
        foreach ($rows as $row) {
            $tds = $row->childNodes;
            foreach ($tds as $index => $td) {
                if ($index % 2 != 0) {
                    continue;
                }
                $header[$index] = preg_replace('/:/', '', $td->nodeValue);
            }
            break;
        }

        $addresses = [];
        foreach ($rows as $index => $row) {
            if ($index == 0) {
                continue;
            }

            $tds = $row->childNodes;
            $address = [];
            foreach ($tds as $tdIndex => $td) {
                if ($tdIndex % 2 != 0) {
                    continue;
                }
                $address[$header[$tdIndex]] = $td->nodeValue;
            }
            array_push($addresses, $address);
        }

        return [
            'error' => false,
            'addresses' => $addresses
        ];
    }

}
