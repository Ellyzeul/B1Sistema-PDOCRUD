<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function read(Request $request)
    {
        return Address::find($request->order_number);
    }

    public function update(Request $request)
    {
        return Address::find($request->order_number)->update($request->address);
    }
}
