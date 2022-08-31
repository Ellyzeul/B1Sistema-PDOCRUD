<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public static function create(Request $request)
    {
        [$response, $status_code] = User::create(
            $request->input('name'),
            $request->input('email'),
            $request->input('password'),
            $request->input('id_section'),
            $request->input('ramal')
        );

        return $response;
    }

    public static function login(Request $request)
    {
        [$response, $status_code] = User::login(
            $request->input('email'),
            $request->input('password')
        );

        return $response;
    }
}
