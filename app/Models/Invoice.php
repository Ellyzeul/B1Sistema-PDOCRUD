<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = ['key', 'emitted_at', 'period', 'type', 'value', 'status', 'manifestation', 'emitter_cnpj', 'recipient_cnpj', 'courier_cnpj', 'has_xml', 'origin', 'cfops', 'match'];

    protected $casts = [
        'value' => 'float',
    ];
    
    protected $hidden = ['emitter_cnpj', 'recipient_cnpj', 'courier_cnpj'];

    protected $appends = ['emitter', 'recipient', 'courier'];

    public function getEmitterAttribute()
    {
        return $this->getInvoiceCompany($this->emitter_cnpj);
    }

    public function getRecipientAttribute()
    {
        return $this->getInvoiceCompany($this->recipient_cnpj);
    }

    public function getCourierAttribute()
    {
        return $this->getInvoiceCompany($this->courier_cnpj);
    }

    private function getInvoiceCompany(?string $cnpj)
    {
        return InvoiceCompany::find($cnpj);
    }
}
