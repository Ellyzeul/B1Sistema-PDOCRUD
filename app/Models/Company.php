<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    use HasFactory;

    public function readThumbnails()
    {
        $results = DB::select("SELECT id, thumbnail FROM companies");

        $response = [
            "message" => "Thumbnails recuperadas",
            "thumbs" => []
        ];
        foreach($results as $company) {
            array_push($response["thumbs"], [
                "id" => $company->id,
                "thumbnail" => $company->thumbnail
            ]);
        }

        return $response;
    }

    public function readInfo()
    {
        $companies = DB::select("SELECT * FROM companies");
        $companiesSellercentrals = DB::select("
            SELECT 
                id_company,
                (SELECT name FROM sellercentrals WHERE id = id_sellercentral) as sellercentral
            FROM companies_sellercentrals
        ");
        $companiesAccounts = DB::select("SELECT * FROM companies_accounts");
        $response = [];

        foreach($companies as $company) {
            $sellercentrals = $this->filterByCompany($companiesSellercentrals, $company->id, function($sellercentral) {
                return $sellercentral->sellercentral;
            });
            $accounts = $this->filterByCompany($companiesAccounts, $company->id, function($account) {
                return [
                    "bank" => "{$account->id_bank} - {$account->name}",
                    "account" => $account->account,
                    "agency" => $account->agency
                ];
            });
            $toAppend = [
                "company_name" => $company->company_name,
                "fantasy_name" => $company->name,
                "address" => $company->address,
                "cnpj" => $company->cnpj,
                "state_registration" => $company->state_registration,
                "municipal_registration" => $company->municipal_registration,
                "accounts" => $accounts,
                "sellercentrals" => $sellercentrals
            ];
            array_push($response, $toAppend);
        }

        return $response;
    }

    private function filterByCompany(array $raw, mixed $equalityCondition, callable $getProp)
    {
        $filtered = array_filter($raw, 
            function($elem) use ($equalityCondition) {
                return $elem->id_company == $equalityCondition;
            }
        );

        $processed = [];
        foreach($filtered as $elem) {
            array_push($processed, $getProp($elem));
        }

        return $processed;
    }
}
