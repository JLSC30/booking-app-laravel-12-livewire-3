<?php

namespace App\Exports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ServiceExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Service::select('id', 'name', 'description', 'price', 'duration', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Description', 'Price', 'Duration', 'Created At']; // Custom headers
    }

    public function map($service): array
    {
        return [
            $service->id,
            $service->name,
            $service->description,
            $service->price,
            $service->duration,
            $service->created_at->format('Y-m-d H:i:s'), // Format date nicely
        ];
    }
}
