<?php

namespace App\Http\Controllers;

use App\Services\OfferService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function delete(Request $request)
    {
        return (new OfferService())->delete($request);
    }
}
