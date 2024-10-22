<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Service;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportServiceTransactionExport implements FromQuery, WithHeadings, WithMapping, WithEvents
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
        $service =  Service::query()
            ->with(['user' => ['employee'], 'vehicle' => ['customer']])
            ->withCount(['service_repairs', 'service_products']);

        if ($this->start_date !== null && $this->end_date !== null) {
            $service = $service->whereDate('finished_date', '>=', $this->start_date)
                ->whereDate('finished_date', '<=', $this->end_date);
        } else {
            $service = $service->whereMonth('finished_date', Carbon::today()->format('m'));
        };

        return $service->where('status', 'finish');
    }

    public function headings(): array
    {
        return [
            'KODE TRANSAKSI',
            'TANGGAL TRANSAKSI',
            'NAMA PELANGGAN',
            'PLAT NOMOR',
            'NAMA KENDARAAN',
            'JENIS BAYAR',
            'TAMBAHAN BIAYA',
            'TOTAL SERVICE',
            'TOTAL INVOICE'
        ];
    }

    public function map($service): array
    {
        return [
            $service->service_code,
            $service->created_at->format('d/m/Y'),
            $service->vehicle->customer->name,
            $service->vehicle->plate_number,
            $service->vehicle->name,
            $service->payment->name,
            $service->extra_pay,
            $service->total,
            $service->extra_pay + $service->total
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(15);
            },

        ];
    }
}
