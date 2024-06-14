<x-filament-widgets::widget>
    <x-filament::section>
        <marquee>
            <x-heroicon-o-megaphone class="h-5 w-5 inline-block" />
            @foreach ($messages as $message)
                {{ $message }}
            @endforeach
            <x-heroicon-o-megaphone class="h-5 w-5 inline-block" />
        </marquee>
    </x-filament::section>
</x-filament-widgets::widget>
