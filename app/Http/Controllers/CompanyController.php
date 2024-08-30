<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    private Company $company;

    public function __construct()
    {
        $this->company = new Company();
    }

    public function readThumbnails()
    {
        $response = $this->company->readThumbnails();
        return $response;
    }

    public function readInfo()
    {
        $response = $this->company->readInfo();
        return $response;
    }

    public function bankAccounts()
    {
        return Cache::remember('bank_accounts', 86400, fn() => DB::table('companies_accounts')->get());
    }
}
