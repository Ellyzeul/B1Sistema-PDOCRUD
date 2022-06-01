<?php namespace B1system\Route;

use B1system\Controller\OrderController;
use B1system\Controller\SupplierURLController;


class APIRouter
{
	public static function call(array $uriParts, array $request)
	{
		$model = $uriParts[1];
		$operation = $uriParts[2];
		$fileType = $uriParts[3] ?? "json";
		
		if($model == "orders") {
			if($operation == "insert") {
				if($fileType == "json" || $fileType == "") {
					return OrderController::insertWithJSON();
				}
				if($fileType == "xlsx") {
					return OrderController::insertWithXLSX();
				}
			}
		}
		if($model == "supplier_url") {
			if($operation == "read") {
				return SupplierURLController::read(
					intval($request['GET']['id'])
				);
			}
			if($operation == "update") {
				return SupplierURLController::update(
					intval($request['POST']['id']),
					$request['POST']['supplier_url']
				);
			}
		}
	}
}
