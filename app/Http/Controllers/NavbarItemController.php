<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\NavbarItem;

class NavbarItemController extends Controller
{
    public static function read(string $email)
    {
        $navbarItem = new NavbarItem();
        $response = $navbarItem->read($email);
        Log::info($response["message"]);

        return $response;
    }
}
