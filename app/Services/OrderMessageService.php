<?php namespace App\Services;

use Illuminate\Http\Request;
use App\Actions\OrderMessage\AskRatingMessage\SendAskRatingAmazonAction;
use App\Actions\OrderMessage\AskRatingMessage\SendAskRatingEstanteAction;
use App\Actions\OrderMessage\AskRatingMessage\GetAskRatingWhatsappAction;
use App\Actions\OrderMessage\AskRatingMessage\SendAskRatingFNACAction;

class OrderMessageService
{
	private array $handlers = [];

	public function __construct()
	{
		$this->handlers = [
			'Amazon-BR-0' => new SendAskRatingAmazonAction(), 
			'Amazon-CA-0' => new SendAskRatingAmazonAction(), 
			'Amazon-US-0' => new SendAskRatingAmazonAction(), 
			'Amazon-UK-0' => new SendAskRatingAmazonAction(), 
			'Amazon-BR-1' => new SendAskRatingAmazonAction(), 
			'FNAC-PT-0' => new SendAskRatingFNACAction('pt', 0), 
			'FNAC-ES-0' => new SendAskRatingFNACAction('es', 0), 
			'FNAC-PT-1' => new SendAskRatingFNACAction('pt', 1), 
			'Estante-BR-0' => new SendAskRatingEstanteAction(), 
		];
	}

	public function sendAskRating(Request $request)
	{
			$orderId = $request->input('order_id');
			$companyId = $request->input('company_id');
			$sellercentral = $request->input('seller_central');
			$handlerKey = "$sellercentral-$companyId";
			
			try {
				return [
					'success' => true, 
					'content' => $this->handlers[$handlerKey]->handle($orderId)
				];
			}
			catch(\Exception $_) {
				return [ 'success' => false, 'content' => [ 
					'message' => 'Serviço indisponível para esse canal de venda nesta empresa'
				] ];
			}
	}

	public function sendAskRatingAmazon(Request $request)
	{
		$orderId = $request->input('order_id');

		return (new SendAskRatingAmazonAction())->handle($orderId);
	}

	public function sendAskRatingEstante(Request $request)
	{
		$orderId = $request->input('order_id');

		return (new SendAskRatingEstanteAction())->handle($orderId);
	}    

	public function getAskRatingWhatsapp(Request $request)
	{
		return (new GetAskRatingWhatsappAction())->handle($request);
	}   
}