<div>
    {{ $this->form }}

    <x-filament::button wire:click="create" style="margin-top: 16px;">
        {{ __('form.create') }}
    </x-filament::button>

    <x-filament-actions::modals />
</div>
