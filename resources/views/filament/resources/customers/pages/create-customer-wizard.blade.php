<x-filament-panels::page>
    <form wire:submit.prevent="create">
        {{ $this->form }}
    </form>
</x-filament-panels::page>