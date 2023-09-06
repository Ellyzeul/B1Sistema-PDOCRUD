<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InventoryService;

class InventoryController extends Controller
{
    public static function searchInventory(Request $request)
    {
        return (new InventoryService())->searchInventory($request);
    }

    public static function verifyListInventory(Request $request)
    {
        return (new InventoryService())->verifyListInventory($request);
    }

    public static function insertOrUpdateInventory(Request $request)
    {
        return (new InventoryService())->insertOrUpdateInventory($request);
    }    

    public static function deleteFromInventory(Request $request)
    {
        return (new InventoryService())->deleteFromInventory($request);
    }     
    
    public static function updateAvaliableQuantityInventory(Request $request)
    {
        return (new InventoryService())->updateAvaliableQuantityInventory($request);
    }      
}
