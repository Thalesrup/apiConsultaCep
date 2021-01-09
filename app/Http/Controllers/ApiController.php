<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CepRequest;
use App\Http\Requests\LogadouroRequest;
use App\Services\BuscaCepLogadouro;

class ApiController extends Controller
{
    private $requisicao;

    public function getCep(Request $request)
    {
        $this->requisicao = new BuscaCepLogadouro();
        $responseCorreios = $this->requisicao->buscar($request->cep);
        return response()->json($responseCorreios);
//        return response()->json(['msg' => "Cep Buscado $request->cep"], 200);
    }

    public function getLogadouro(Request $request)
    {
        return response()->json(['msg' => "Logadouro Buscado $request->logadouro"], 200);
    }

}
