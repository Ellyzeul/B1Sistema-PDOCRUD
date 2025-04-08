<?php

namespace App\Http\Controllers;

use App\Actions\EmittedInvoice\CreateAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmittedInvoiceController extends Controller
{
    public function create(Request $request)
    {
        return (new CreateAction())->handle($request);
    }
}
