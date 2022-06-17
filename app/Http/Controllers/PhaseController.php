<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phase;

class PhaseController extends Controller
{
    public static function read()
    {
        $response = Phase::read();

        return $response;
    }
}
