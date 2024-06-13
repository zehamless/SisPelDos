<x-filament-widgets::widget>
    <x-filament::section>

                <marquee>
                    @foreach ($messages as $message)
                        {{ $message }}
                    @endforeach
                </marquee>


    </x-filament::section>
</x-filament-widgets::widget>
