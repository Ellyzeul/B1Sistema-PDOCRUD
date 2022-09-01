<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

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
}
