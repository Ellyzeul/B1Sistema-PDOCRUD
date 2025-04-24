<?php

namespace App\Http\Controllers;

use App\Actions\EmittedInvoice\CreateAction;
use App\Http\Controllers\Controller;
use App\Models\EmittedInvoice;
use Illuminate\Http\Request;

class EmittedInvoiceController extends Controller
{
    public function read() {
        return EmittedInvoice::where('emitted_at', '<>', null)
            ->orderBy('emitted_at', 'desc')
            ->get()
            ->values();
    }

    public function create(Request $request)
    {
        return (new CreateAction())->handle($request);
    }
}
