<x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
    <h2 class="text-xl font-semibold mb-6 text-neutral/80 dark:text-gray-100">
        {{ __('Export tables') }}
    </h2>

    <!-- Branch Export -->
    <div class="flex flex-col md:flex-row gap-4 md:items-end mb-8">
        <flux:select wire:model.live="formatBranch" label="Export Branch Format" placeholder="Select format"
            icon="o-document-arrow-down"
            class="w-full !ring-black focus:!ring-black dark:!ring-white dark:focus:!ring-white">
            <flux:select.option value="csv">CSV</flux:select.option>
            <flux:select.option value="xlsx">Excel (XLSX)</flux:select.option>
            <flux:select.option value="pdf">PDF</flux:select.option>
        </flux:select>

        <x-mary-button wire:click="export_branches" wire:loading.attr="disabled" icon="o-arrow-down-tray"
            class="btn-primary w-full md:w-auto" spinner>
            <span wire:loading.remove>
                Export branch table to {{ strtoupper($formatBranch) }}
            </span>
            <span wire:loading>Exporting...</span>
        </x-mary-button>
    </div>

    <!-- Services Export -->
    <div class="flex flex-col md:flex-row gap-4 md:items-end mb-8">
        <flux:select wire:model.live="formatService" label="Export Service Format" placeholder="Select format"
            icon="o-document-arrow-down"
            class="w-full !ring-black focus:!ring-black dark:!ring-white dark:focus:!ring-white">
            <flux:select.option value="csv">CSV</flux:select.option>
            <flux:select.option value="xlsx">Excel (XLSX)</flux:select.option>
            <flux:select.option value="pdf">PDF</flux:select.option>
        </flux:select>

        <x-mary-button wire:click="export_services" wire:loading.attr="disabled" icon="o-arrow-down-tray"
            class="btn-primary w-full md:w-auto" spinner>
            <span wire:loading.remove>
                Export services table to {{ strtoupper($formatService) }}
            </span>
            <span wire:loading>Exporting...</span>
        </x-mary-button>
    </div>

    <!-- Users Export -->
    <div class="flex flex-col md:flex-row gap-4 md:items-end mb-8">
        <flux:select wire:model.live="formatUser" label="Export User Format" placeholder="Select format"
            icon="o-document-arrow-down"
            class="w-full !ring-black focus:!ring-black dark:!ring-white dark:focus:!ring-white">
            <flux:select.option value="csv">CSV</flux:select.option>
            <flux:select.option value="xlsx">Excel (XLSX)</flux:select.option>
            <flux:select.option value="pdf">PDF</flux:select.option>
        </flux:select>

        <x-mary-button wire:click="export_users" wire:loading.attr="disabled" icon="o-arrow-down-tray"
            class="btn-primary w-full md:w-auto" spinner>
            <span wire:loading.remove>
                Export users table to {{ strtoupper($formatUser) }}
            </span>
            <span wire:loading>Exporting...</span>
        </x-mary-button>
    </div>
</x-mary-card>
