<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BlacklistService;

class BlacklistController extends Controller
{
    public static function readBlacklistFromInterval(Request $request)
    {
        return (new BlacklistService())->readBlacklistFromInterval($request);
    }
    
    public static function insertOrUpdateBlacklist(Request $request)
    {
        return (new BlacklistService())->insertOrUpdateBlacklist($request);
    }

    public static function deleteFromBlacklist(Request $request)
    {
        return (new BlacklistService())->deleteFromBlacklist($request);
    }

    public static function searchBlacklist(Request $request)
    {
        return (new BlacklistService())->searchBlacklist($request);
    }

    public static function verifyListBlacklist(Request $request)
    {
        return (new BlacklistService())->verifyListBlacklist($request);
    }
}
