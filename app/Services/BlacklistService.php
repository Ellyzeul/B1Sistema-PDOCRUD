<?php namespace App\Services;

use Illuminate\Http\Request;

use App\Actions\Blacklist\ReadBlacklistFromIntervalAction;
use App\Actions\Blacklist\InsertOrUpdateBlacklistAction;
use App\Actions\Blacklist\DeleteFromBlacklistAction;
use App\Actions\Blacklist\SearchBlacklistAction;
use App\Actions\Blacklist\VerifyListBlacklistAction;

class BlacklistService
{
    public function readBlacklistFromInterval(Request $request)
    {
        return (new ReadBlacklistFromIntervalAction())->handle($request);  
    }

    public function insertOrUpdateBlacklist(Request $request)
    {
        return (new InsertOrUpdateBlacklistAction())->handle($request);
    }

    public function deleteFromBlacklist(Request $request)
    {
        return (new DeleteFromBlacklistAction())->handle($request);
    }

    public function searchBlacklist(Request $request)
    {
        return (new SearchBlacklistAction())->handle($request);
    }

    public function verifyListBlacklist(Request $request)
    {
        return (new VerifyListBlacklistAction())->handle($request);
    }
}