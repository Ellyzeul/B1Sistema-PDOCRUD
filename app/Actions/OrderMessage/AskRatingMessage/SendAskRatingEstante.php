<?php namespace App\Actions\OrderMessage\AskRatingMessage;

use Illuminate\Http\Request;
use App\Actions\OrderMessage\Traits\AskRatingMessageCommon;

class SendAskRatingEstante
{
    use AskRatingMessageCommon;

    public function handle(Request $request)
    {
        $orderId = $request->input('order_id');

        return $orderId;
    }

}
