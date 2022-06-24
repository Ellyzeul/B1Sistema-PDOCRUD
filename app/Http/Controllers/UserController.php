<?php namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public static function create(string $name, string $email, string $password, int $id_section)
    {
        [$response, $status_code] = User::create(
            $name, 
            $email, 
            $password, 
            $id_section
        );

        return $response;
    }

    public static function login(string $email, string $password)
    {
        [$response, $status_code] = User::login($email, $password);

        return $response;
    }
}
