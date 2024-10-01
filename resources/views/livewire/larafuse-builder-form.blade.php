<div>
    {{ $this->form }}

    <x-filament::button wire:click="create" style="margin-top: 16px;">
        Create
    </x-filament::button>

    <x-filament-actions::modals />
</div>
