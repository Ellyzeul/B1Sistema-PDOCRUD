<?php

namespace App\Http\Controllers;

use App\Services\B1RastreamentoService;
use Illuminate\Http\Request;

class B1RastreamentoController extends Controller
{
    public function index(Request $request)
    {
        return (new B1RastreamentoService())->orderPhase($request);
    }
}
