<?php

namespace App\Http\Controllers;

use App\Actions\Address\UpdateAddressAction;
use App\Actions\Tracking\ConsultPostalCodeAction;
use App\Models\Address;
use App\Models\EmittedInvoice;
use App\Models\Order;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function read(Request $request)
    {
        $address = Address::find($request->order_number);

        return [
            'address' => $address,
            'order' => Order::find($request->order_id),
            'items' => Order::where('online_order_number', $request->order_number)->get(),
            'validate_address' => (new ConsultPostalCodeAction())->handle(new Request([
                'zip_code' => $address->postal_code,
            ])),
            'invoice' => EmittedInvoice::where('order_number', $request->order_number)->first(),
        ];
    }

    public function update(Request $request)
    {
        return (new UpdateAddressAction())->handle($request);
    }
}
