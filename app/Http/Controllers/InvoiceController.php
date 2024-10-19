<?php

namespace App\Http\Controllers;

use App\Actions\Invoice\CreateBatchAction;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function createBatch(Request $request)
    {
        return (new CreateBatchAction())->handle($request);
    }
}
