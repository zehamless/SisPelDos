<!-- resources/views/components/logout-button.blade.php -->
@props([
    'action' => filament()->getLogoutUrl(),
    'color' => 'primary',
    'icon' => \Filament\Support\Facades\FilamentIcon::resolve('panels::user-menu.logout-button') ?? 'heroicon-m-arrow-left-on-rectangle',
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
