<?php

namespace App\Exports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BranchExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Branch::select('id', 'name', 'address', 'is_active', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Address', 'Is Active', 'Created At']; // Custom headers
    }

    public function map($branch): array
    {
        return [
            $branch->id,
            $branch->name,
            $branch->address,
            $branch->is_active,
            $branch->created_at->format('Y-m-d H:i:s'), // Format date nicely
        ];
    }
}
