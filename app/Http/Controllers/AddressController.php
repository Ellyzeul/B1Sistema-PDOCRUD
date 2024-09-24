<?php

namespace App\Http\Controllers;

use App\Actions\Address\UpdateAddressAction;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    public function read(Request $request)
    {
        return [
            'address' => Address::find($request->order_number),
            'order' => Order::find($request->order_id),
        ];
    }

    public function update(Request $request)
    {
        return (new UpdateAddressAction())->handle($request);
    }
}
