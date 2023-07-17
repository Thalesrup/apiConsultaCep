<?php

namespace App\Http\Controllers;


class ApiController extends Controller
{

    static $tipoSaida;
    static $clause = [];

    public function sendResponse($success = true, $message = '', $dataCallback = [], $codeHttp = 200)
    {
        $response = [
            'success' => $success,
            'message' => $message,
            'data'    => $dataCallback,
        ];
        
        return response()->json($response, $codeHttp);
    }

    static function getTypeOutout()
    {
        return self::$tipoSaida;
    }

    static function getClauseQuery()
    {
        return self::$clause;
    }

    static function setTypeOutput($saida)
    {
        self::$tipoSaida = $saida;
    }

    static function setClauseQuery($clause)
    {
        self::$clause = $clause;
    }

}
