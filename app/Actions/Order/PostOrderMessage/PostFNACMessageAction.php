<?php namespace App\Actions\Order\PostOrderMessage;

use App\Services\ThirdParty\FNAC;

class PostFNACMessageAction
{
  public function handle(string $text, array $toAnswer)
  {
    $messageId = $toAnswer['id'];

    return (new FNAC(0))->messagesUpdate($messageId, $text);
  }
}
