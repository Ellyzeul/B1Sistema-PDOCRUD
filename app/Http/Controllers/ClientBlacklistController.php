<?php

namespace App\Http\Controllers;

use App\Models\ClientBlacklist;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientBlacklistController extends Controller
{
    public function index(Request $request)
    {
        return ClientBlacklist::where('key', 'like', $request->key)->get();
    }

    public function create(Request $request)
    {
        try {
            return response(ClientBlacklist::create([
                'key' => $request->key,
                'type' => $request->type,
            ]), 201);
        }
        catch(QueryException) {
            return response([
                'err_msg' => Str::upper($request->type) . " $request->key já registrado..."
            ], 400);
        }
        catch(Exception) {
            return response([
                'err_msg' => 'Erro interno ocorreu...',
            ], 500);
        }
    }
}
