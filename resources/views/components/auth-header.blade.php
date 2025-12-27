@props(['title', 'description'])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl" class="text-base-content">{{ $title }}</flux:heading>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>
