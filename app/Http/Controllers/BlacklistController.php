<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BlacklistService;

class BlacklistController extends Controller
{
    public static function ReadBlacklistFromInterval(Request $request)
    {
        return (new BlacklistService())->readBlacklistFromInterval($request);
    }
    
    public static function InsertOrUpdateBlacklist(Request $request)
    {
        return (new BlacklistService())->insertOrUpdateBlacklist($request);
    }

    public static function DeleteFromBlacklist(Request $request)
    {
        return (new BlacklistService())->deleteFromBlacklist($request);
    }

    public static function SearchBlacklist(Request $request)
    {
        return (new BlacklistService())->searchBlacklist($request);
    }

    public static function VerifyListBlacklist(Request $request)
    {
        return (new BlacklistService())->verifyListBlacklist($request);
    }
}
