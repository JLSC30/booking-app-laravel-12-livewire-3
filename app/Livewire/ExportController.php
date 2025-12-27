<?php

namespace App\Livewire;

use App\Exports\BranchExport;
use App\Exports\ServiceExport;
use App\Exports\UserExport;
use App\Models\Export;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Component
{
    use AuthorizesRequests, Toast;

    public string $formatBranch = 'csv';

    public string $formatService = 'csv';

    public string $formatUser = 'csv';

    public function mount(): void
    {
        $this->authorize('viewAny', Export::class);
    }

    public function export_branches(): BinaryFileResponse
    {
        $format = match ($this->formatBranch) {
            'csv' => 'csv',
            'xlsx' => 'xlsx',
            'pdf' => 'pdf',
            default => 'csv',  // Fallback to csv
        };

        $writerType = match ($format) {
            'csv' => 'Csv',
            'xlsx' => 'Xlsx',
            'pdf' => 'Dompdf',
            default => 'Csv',
        };

        $filename = 'branches_'.now()->format('Y-m-d').'.'.$format;

        $this->success('Branch exported successfully.');

        return Excel::download(new BranchExport, $filename, $writerType);
    }

    public function export_services(): BinaryFileResponse
    {
        $format = match ($this->formatService) {
            'csv' => 'csv',
            'xlsx' => 'xlsx',
            'pdf' => 'pdf',
            default => 'csv',  // Fallback to csv
        };

        $writerType = match ($format) {
            'csv' => 'Csv',
            'xlsx' => 'Xlsx',
            'pdf' => 'Dompdf',
            default => 'Csv',
        };

        $filename = 'services_'.now()->format('Y-m-d').'.'.$format;

        $this->success('Service exported successfully.');

        return Excel::download(new ServiceExport, $filename, $writerType);
    }

    public function export_users(): BinaryFileResponse
    {
        $format = match ($this->formatUser) {
            'csv' => 'csv',
            'xlsx' => 'xlsx',
            'pdf' => 'pdf',
            default => 'csv',  // Fallback to csv
        };

        $writerType = match ($format) {
            'csv' => 'Csv',
            'xlsx' => 'Xlsx',
            'pdf' => 'Dompdf',
            default => 'Csv',
        };

        $filename = 'users_'.now()->format('Y-m-d').'.'.$format;

        $this->success('User exported successfully.');

        return Excel::download(new UserExport, $filename, $writerType);
    }

    // public function export_users(): BinaryFileResponse
    // {
    //     $filename = 'users_' . now()->format('Y-m-d') . '.' . $this->formatUser;
    //     // Map format to Maatwebsite writer type
    //     $writerType = match ($this->formatUser) {
    //        'csv'  => 'Csv',
    //         'xlsx' => 'Xlsx',
    //         'pdf'  => 'Dompdf',
    //         default => 'Csv',
    //     };

    //     $this->success("User exported successfully.");
    //     return Excel::download(new UserExport(), $filename, $writerType);
    // }

    public function render()
    {
        return view('livewire.export-index');
    }
}
