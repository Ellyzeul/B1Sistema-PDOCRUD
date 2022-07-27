<?php namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User
{
    public static function create(string $name, string $email, string $password, int $id_section) {
        $inserted = DB::table('users')->insert([
            'email' => $email,
            'name' => $name,
            'password' => Hash::make($password),
            'token' => Str::random(60),
            'id_section' => $id_section
        ]);

        $response = $inserted
            ? [[
                "message" => "Usuário inserido com sucesso"
            ], 201]
            : [[
                "message" => "Erro ao inserir o usuário"
            ], 500];
        
        return $response;
    }

    public static function login(string $email, string $password) {
        $user = DB::table('users')->where("email", $email)->get()->first();

        if($user == null) return [[
            "message" => "E-mail não cadastrado no sistema"
        ], 401];
        if(!Hash::check($password, $user->password)) return [[
            "message" => "Senha incorreta"
        ], 401];

        if(Hash::needsRehash($user->password)) DB::table('table')->update([
            "password" => Hash::make($password)
        ]);

        return [[
            "message" => "Login realizado",
            "email" => $email,
            "name" => $user->name,
            "token" => $user->token,
            "id_section" => $user->id_section
        ], 200];
    }
}
