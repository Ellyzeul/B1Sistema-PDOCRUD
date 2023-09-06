<?php namespace App\Services;

use Illuminate\Http\Request;

use App\Actions\Inventory\SearchInventoryAction;
use App\Actions\Inventory\VerifyListInventoryAction;
use App\Actions\Inventory\InsertOrUpdateInventoryAction;
use App\Actions\Inventory\DeleteFromInventoryAction;
use App\Actions\Inventory\UpdateAvaliableQuantityInventoryAction;

class InventoryService
{
    public function searchInventory(Request $request)
    {
        return (new SearchInventoryAction())->handle($request);
    }

    public function verifyListInventory(Request $request)
    {
        return (new VerifyListInventoryAction())->handle($request);
    }

    public function insertOrUpdateInventory(Request $request)
    {
        return (new InsertOrUpdateInventoryAction())->handle($request);
    }   
    
    public function deleteFromInventory(Request $request)
    {
        return (new DeleteFromInventoryAction())->handle($request);
    }     

    public function updateAvaliableQuantityInventory(Request $request)
    {
        return (new UpdateAvaliableQuantityInventoryAction())->handle($request);
    }     
}