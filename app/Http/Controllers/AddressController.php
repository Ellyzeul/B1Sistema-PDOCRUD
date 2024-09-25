<?php

namespace App\Http\Controllers;

use App\Actions\Address\UpdateAddressAction;
use App\Actions\Tracking\ConsultPostalCodeAction;
use App\Models\Address;
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
            'validate_address' => (new ConsultPostalCodeAction())->handle(new Request([
                'zip_code' => $address->postal_code,
            ])),
        ];
    }

    public function update(Request $request)
    {
        return (new UpdateAddressAction())->handle($request);
    }
}
