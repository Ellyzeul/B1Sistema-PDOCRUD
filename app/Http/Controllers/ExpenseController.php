<?php

namespace App\Http\Controllers;

use App\Actions\Expense\CreateOrUpdateAction;
use App\Actions\Expense\ReadAction;
use App\Actions\Expense\UpdateFiscalAction;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function read(Request $request)
    {
        return (new ReadAction())->handle($request);
    }

    public function createOrUpdate(Request $request)
    {
        return (new CreateOrUpdateAction())->handle($request);
    }

    public function updateFiscal(Request $request)
    {
        return (new UpdateFiscalAction())->handle($request);
    }
}
