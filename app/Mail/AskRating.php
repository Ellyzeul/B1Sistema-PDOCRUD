<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AskRating extends Mailable
{
    use Queueable, SerializesModels;

    private string $sender;
    private string $isNational;
    public string $clientName;
    public string $orderNumber;
    public string $bookName;
    public string $companyName;

    public function __construct(string $sender, bool $isNational, string $clientName, string $orderNumber, string $bookName, string $companyName)
    {
        $this->sender = $sender;
        $this->isNational = $isNational;
        $this->clientName = $clientName;
        $this->orderNumber = $orderNumber;
        $this->bookName = $bookName;
        $this->companyName = $companyName;
    }

    public function build()
    {
        return $this
            ->from($this->sender, $this->companyName)
            ->subject('Atualização do pedido Amazon de ID ' . $this->orderNumber)
            ->markdown($this->isNational
                ? 'emails.ask-rating.national'
                : 'emails.ask-rating.international'
            );
    }
}
