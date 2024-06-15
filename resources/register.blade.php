{{--Replace to vendor/filament/filament/resources/views/pages/auth/register.blade.php--}}
<x-filament-panels::page.simple>
    @if (filament()->hasLogin())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/register.actions.login.before') }}

            {{ $this->loginAction }}
        </x-slot>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.register.form.before') }}

    <x-filament-panels::form wire:submit="register">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
        <x-filament::link href="{{ route('filament.user.pages.dashboard') }}" >
            Kembali ke Dashboard
        </x-filament::link>
    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.register.form.after') }}
</x-filament-panels::page.simple>
