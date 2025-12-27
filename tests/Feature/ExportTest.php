<?php

use App\Exports\BranchExport;
use App\Exports\ServiceExport;
use App\Exports\UserExport;
use App\Livewire\ExportController;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;

beforeEach(function () {
    Date::setTestNow('2025-12-23 14:30:00');

    // Create an admin user
    $this->admin = User::factory()->admin()->create();

    // Or if you don't have the admin() state method, do this:
    // $this->admin = User::factory()->create(['is_admin' => true]);
});

afterEach(function () {
    Date::setTestNow();
});

it('exports branches as CSV', function () {
    Excel::fake();

    Livewire::actingAs($this->admin)
        ->test(ExportController::class)
        ->set('formatBranch', 'csv')
        ->call('export_branches');

    Excel::assertDownloaded('branches_2025-12-23.csv', fn (BranchExport $export) => true, 'Csv');
});

it('exports branches as XLSX', function () {
    Excel::fake();

    Livewire::actingAs($this->admin)
        ->test(ExportController::class)
        ->set('formatBranch', 'xlsx')
        ->call('export_branches');

    Excel::assertDownloaded('branches_2025-12-23.xlsx', fn (BranchExport $export) => true, 'Xlsx');
});

it('exports branches as PDF', function () {
    Excel::fake();

    Livewire::actingAs($this->admin)
        ->test(ExportController::class)
        ->set('formatBranch', 'pdf')
        ->call('export_branches');

    Excel::assertDownloaded('branches_2025-12-23.pdf', fn (BranchExport $export) => true, 'Dompdf');
});

it('exports services as CSV', function () {
    Excel::fake();

    Livewire::actingAs($this->admin)
        ->test(ExportController::class)
        ->set('formatService', 'csv')
        ->call('export_services');

    Excel::assertDownloaded('services_2025-12-23.csv', fn (ServiceExport $export) => true, 'Csv');
});

it('exports services as XLSX', function () {
    Excel::fake();

    Livewire::actingAs($this->admin)
        ->test(ExportController::class)
        ->set('formatService', 'xlsx')
        ->call('export_services');

    Excel::assertDownloaded('services_2025-12-23.xlsx', fn (ServiceExport $export) => true, 'Xlsx');
});

it('exports services as PDF', function () {
    Excel::fake();

    Livewire::actingAs($this->admin)
        ->test(ExportController::class)
        ->set('formatService', 'pdf')
        ->call('export_services');

    Excel::assertDownloaded('services_2025-12-23.pdf', fn (ServiceExport $export) => true, 'Dompdf');
});

it('exports users as CSV', function () {
    Excel::fake();

    Livewire::actingAs($this->admin)
        ->test(ExportController::class)
        ->set('formatUser', 'csv')
        ->call('export_users');

    Excel::assertDownloaded('users_2025-12-23.csv', fn (UserExport $export) => true, 'Csv');
});

it('exports users as XLSX', function () {
    Excel::fake();

    Livewire::actingAs($this->admin)
        ->test(ExportController::class)
        ->set('formatUser', 'xlsx')
        ->call('export_users');

    Excel::assertDownloaded('users_2025-12-23.xlsx', fn (UserExport $export) => true, 'Xlsx');
});

it('exports users as PDF', function () {
    Excel::fake();

    Livewire::actingAs($this->admin)
        ->test(ExportController::class)
        ->set('formatUser', 'pdf')
        ->call('export_users');

    Excel::assertDownloaded('users_2025-12-23.pdf', fn (UserExport $export) => true, 'Dompdf');
});

it('falls back to CSV when an invalid format is provided', function () {
    Excel::fake();

    Livewire::actingAs($this->admin)
        ->test(ExportController::class)
        ->set('formatBranch', 'invalid')
        ->call('export_branches');

    Excel::assertDownloaded('branches_2025-12-23.csv', fn (BranchExport $export) => true, 'Csv');
});
