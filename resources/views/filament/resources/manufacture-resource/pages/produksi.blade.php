<x-filament-panels::page>
    <x-filament-panels::form wire:submit>
        {{ $this->form }}
    </x-filament-panels::form>
    <div class="pt-3 sm:pt-5">
        {{ $this->notes }}
    </div>
    <x-filament-panels::form.actions
        :actions="$this->getFormActions()"
    />
</x-filament-panels::page>