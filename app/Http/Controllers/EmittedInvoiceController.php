<?php

namespace App\Http\Controllers;

use App\Actions\EmittedInvoice\CreateAction;
use App\Http\Controllers\Controller;
use App\Models\EmittedInvoice;
use Illuminate\Http\Request;

class EmittedInvoiceController extends Controller
{
    public function read() {
        return EmittedInvoice::orderBy('emitted_at', 'desc')
            ->orderBy('number', 'desc')
            ->get()
            ->values();
    }

    public function create(Request $request)
    {
        return (new CreateAction())->handle($request);
    }
}
