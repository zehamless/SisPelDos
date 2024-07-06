<!-- resources/views/components/logout-button.blade.php -->
@props([
    'action' => filament()->getLogoutUrl(),
    'color' => 'primary',
    'icon' => 'heroicon-m-arrow-left-start-on-rectangle',
])

<form method="POST" action="{{ $action }}">
    @csrf
    <x-filament::button
        type="submit"
        :color="$color"
        :icon="$icon"
    >
        {{ __('filament-panels::layout.actions.logout.label') }}
    </x-filament::button>
</form>
