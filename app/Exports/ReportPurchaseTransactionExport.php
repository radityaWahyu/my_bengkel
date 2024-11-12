<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Purchase;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportPurchaseTransactionExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    protected $start_date;
    protected $end_date;


    public function __construct($start_date = null, $end_date = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $purchase = Purchase::query()
            ->with(['purchase_products' => ['product'], 'supplier'])
            ->withCount(['purchase_products']);

        if ($this->start_date !== null && $this->end_date !== null) {
            $purchase = $purchase->whereDate('created_at', '>=', $this->start_date)
                ->whereDate('created_at', '<=', $this->end_date);
        } else {
            $purchase = $purchase->whereMonth('created_at', Carbon::today()->format('m'));
        };

        return $purchase->where('status', 'finish');
    }

    public function headings(): array
    {
        return [
            'KODE TRANSAKSI',
            'TANGGAL TRANSAKSI',
            'PEMASOK',
            'NO NOTA',
            'JENIS BAYAR',
            'TAMBAHAN BIAYA',
            'SUB TOTAL',
            'TOTAL INVOICE'
        ];
    }

    public function map($purchase): array
    {
        return [
            $purchase->purchase_code,
            $purchase->created_at->format('d/m/Y'),
            $purchase->supplier->name,
            $purchase->invoice_number,
            $purchase->payment->name,
            $purchase->extra_pay,
            $purchase->total,
            $purchase->extra_pay + $purchase->total
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(15);
            },

        ];
    }
}
