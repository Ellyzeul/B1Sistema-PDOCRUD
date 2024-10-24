<?php

namespace App\Http\Controllers;

use App\Actions\Invoice\GetPurchaseItemsAction;
use App\Actions\Invoice\LinkPurchaseItemsAction;
use App\Actions\Invoice\ReadAction;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function read(Request $request)
    {
        return (new ReadAction())->handle($request);
    }

    public function getPurchaseItems(Request $request)
    {
        return (new GetPurchaseItemsAction())->handle($request);
    }

    public function linkPurchaseItems(Request $request)
    {
        return (new LinkPurchaseItemsAction())->handle($request);
    }
}
