<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JurnalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transaction_type = "-";
        $transaction_code = "-";


        if (!empty($this->transactable_id))
            switch ($this->transactable_type) {
                case 'App\Models\Service':
                    $transaction_type = 'Service';
                    $transaction_code = $this->transactable->service_code;
                    break;

                case 'App\Models\Sale':
                    $transaction_type = 'Penjualan';
                    $transaction_code = $this->transactable->sale_code;
                    break;

                case 'App\Models\Purchase':
                    $transaction_type = 'Pembelian';
                    $transaction_code = $this->transactable->purchase->purchase_code;
                    break;
            }

        return [
            'id' => $this->id,
            'jurnal_code' => $this->jurnal_code,
            'income' => (int)$this->income,
            'expense' => (int)$this->expense,
            'transaction_id' => empty($this->transactable_id) ? null : $this->transactable->id,
            'transaction_type' => $transaction_type,
            'transaction_code' => $transaction_code,
            'transaction_date' => $this->transaction_date->format('d/m/Y'),
            'payment' => $this->payment->name,
            'description' => $this->description,
            'employee' => $this->user->employee->name,
        ];
    }
}
