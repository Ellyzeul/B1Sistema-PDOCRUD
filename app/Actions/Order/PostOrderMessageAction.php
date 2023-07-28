<?php namespace App\Actions\Order;

use App\Actions\Order\PostOrderMessage\PostFNACMessageAction;
use App\Actions\Order\PostOrderMessage\PostMercadoLivreMessageAction;
use Illuminate\Http\Request;

class PostOrderMessageAction
{
  public function handle(Request $request)
  {
    $sellercentral = $request->input('sellercentral');
    $company = $request->input('company');
    $text = $request->input('text');
    $toAnswer = $request->input('to_answer');

    if($sellercentral === 'fnac') return (new PostFNACMessageAction())->handle($text, $toAnswer);
    if($sellercentral === 'mercado-livre') return (new PostMercadoLivreMessageAction())->handle($text, $company, $toAnswer);

    throw new \Exception("Unknown sellercentral: $sellercentral");
  }
}
