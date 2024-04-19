<?php namespace App\Actions\OrderMessage\Traits;

trait OrderMessageCommon
{
  private function getCompanyName(int $idCompany)
  {
    if($idCompany === 0) return 'seline';
    if($idCompany === 1) return 'b1';
    if($idCompany === 2) return 'j1';
    if($idCompany === 3) return 'r1';

    throw new \Exception("Nenhuma empresa com ID $idCompany encontrada...");
  }
}
