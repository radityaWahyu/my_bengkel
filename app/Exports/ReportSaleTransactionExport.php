<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Sale;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportSaleTransactionExport implements FromQuery, WithHeadings, WithMapping, WithEvents
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
        $sale = Sale::query()
            ->with(['sale_products' => ['product']])
            ->withCount(['sale_products']);

        if ($this->start_date !== null && $this->end_date !== null) {
            $sale = $sale->whereDate('created_at', '>=', $this->start_date)
                ->whereDate('created_at', '<=', $this->end_date);
        } else {
            $sale = $sale->whereMonth('created_at', Carbon::today()->format('m'));
        };

        return $sale->where('status', 'finish');
    }

    public function headings(): array
    {
        return [
            'KODE TRANSAKSI',
            'TANGGAL TRANSAKSI',
            'JENIS BAYAR',
            'TAMBAHAN BIAYA',
            'SUB TOTAL',
            'TOTAL INVOICE'
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->sale_code,
            $sale->created_at->format('d/m/Y'),
            $sale->payment->name,
            $sale->extra_pay,
            $sale->total,
            $sale->extra_pay + $sale->total
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
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
            },

        ];
    }
}
