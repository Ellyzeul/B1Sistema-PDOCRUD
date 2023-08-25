<?php namespace App\Actions\OrderMessage\AskRatingMessage;

class SendAskRatingFNACAction
{
  private string $country;
  private int $idCompany;

  public function __construct(string $country, int $idCompany)
  {
    $this->country = $country;
    $this->idCompany = $idCompany;
  }

  public function handle()
  {
    return;
  }
}
