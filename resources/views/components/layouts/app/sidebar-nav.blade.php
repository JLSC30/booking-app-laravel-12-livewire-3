<flux:navlist.group :heading="__('Dashboard')" class="grid">
    <flux:navlist.item icon="home" :href="route('dashboard')" class="!text-neutral/80 dark:!text-gray-100"
        :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
    @can('viewAny', \App\Models\BranchSchedule::class)
        <flux:navlist.item icon="calendar" class="!text-neutral/80 dark:!text-gray-100" :href="route('branch-schedules')"
            :current="request()->routeIs('branch-schedules')" wire:navigate>
            {{ __('Branch Schedules') }}
        </flux:navlist.item>
    @endcan

    @can('viewAny', \App\Models\Appointment::class)
        <flux:navlist.item icon="document-text" class="!text-neutral/80 dark:!text-gray-100" :href="route('appointments')"
            :current="request()->routeIs('appointments')" wire:navigate>
            {{ __('Appointments') }}
        </flux:navlist.item>
    @endcan

</flux:navlist.group>
<flux:navlist.group :heading="__('Configuration')" class="grid">
    @can('viewAny', \App\Models\Branch::class)
        <flux:navlist.item icon="building-storefront" class="!text-neutral/80 dark:!text-gray-100" :href="route('branches')"
            :current="request()->routeIs('branches')" wire:navigate>
            {{ __('Branches') }}
        </flux:navlist.item>
    @endcan

    @can('viewAny', \App\Models\Service::class)
        <flux:navlist.item icon="wrench-screwdriver" class="!text-neutral/80 dark:!text-gray-100" :href="route('services')"
            :current="request()->routeIs('services')" wire:navigate>
            {{ __('Services') }}
        </flux:navlist.item>
    @endcan

    @can('viewAny', \App\Models\User::class)
        <flux:navlist.item icon="user" class="!text-neutral/80 dark:!text-gray-100" :href="route('users')"
            :current="request()->routeIs('users')" wire:navigate>
            {{ __('Users') }}
        </flux:navlist.item>
    @endcan

    @can('viewAny', \App\Models\Export::class)
        <flux:navlist.item icon="presentation-chart-line" class="!text-neutral/80 dark:!text-gray-100"
            :href="route('exports')" :current="request()->routeIs('exports')" wire:navigate>
            {{ __('Exports') }}
        </flux:navlist.item>
    @endcan

</flux:navlist.group>
