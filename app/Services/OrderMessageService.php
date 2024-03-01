<?php namespace App\Services;

use Illuminate\Http\Request;
use App\Actions\OrderMessage\AskRatingMessage\SendAskRatingAmazonAction;
use App\Actions\OrderMessage\AskRatingMessage\SendAskRatingEstanteAction;
use App\Actions\OrderMessage\AskRatingMessage\GetAskRatingWhatsappAction;
use App\Actions\OrderMessage\AskRatingMessage\SendAskRatingFNACAction;
use App\Actions\OrderMessage\AskRatingMessage\SendAskRatingMercadoLivreAction;
use App\Actions\OrderMessage\SendCancellationNoticeAction;

class OrderMessageService
{
	private array $handlers = [];

	public function __construct()
	{
		$this->handlers = [
			'Amazon-BR-0' => new SendAskRatingAmazonAction('Amazon-BR'), 
			'Amazon-CA-0' => new SendAskRatingAmazonAction('Amazon-CA'), 
			'Amazon-ES-0' => new SendAskRatingAmazonAction('Amazon-ES'), 
			'Amazon-US-0' => new SendAskRatingAmazonAction('Amazon-US'), 
			'Amazon-UK-0' => new SendAskRatingAmazonAction('Amazon-UK'), 
			'Amazon-BR-1' => new SendAskRatingAmazonAction('Amazon-BR'), 
			'Amazon-ES-1' => new SendAskRatingAmazonAction('Amazon-ES'), 
			'Amazon-US-1' => new SendAskRatingAmazonAction('Amazon-US'), 
			'Amazon-UK-1' => new SendAskRatingAmazonAction('Amazon-UK'), 
			'FNAC-PT-0' => new SendAskRatingFNACAction('pt', 0), 
			'FNAC-ES-0' => new SendAskRatingFNACAction('es', 0), 
			'FNAC-PT-1' => new SendAskRatingFNACAction('pt', 1), 
			'Estante-BR-0' => new SendAskRatingEstanteAction(), 
			'MercadoLivre-BR-0' => new SendAskRatingMercadoLivreAction(0),
			'MercadoLivre-BR-1' => new SendAskRatingMercadoLivreAction(1),
		];
	}

	public function sendAskRating(Request $request)
	{
		$orderId = $request->input('order_id');
		$companyId = $request->input('company_id');
		$sellercentral = $request->input('seller_central');
		$handlerKey = "$sellercentral-$companyId";
		
		try {
			return $this->handlers[$handlerKey]->handle($orderId);
		}
		catch(\Exception) {
			return [
				'success' => false, 
				'content' => [
					'message' => 'Serviço indisponível para esse canal de venda nesta empresa'
				]
			];
		}
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

	public function sendPreCancellationNotice(Request $request)
	{
		return;
	}

	public function sendCancellationNotice(Request $request)
	{
		return (new SendCancellationNoticeAction())->handle($request);
	}
}