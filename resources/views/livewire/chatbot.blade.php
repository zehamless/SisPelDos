<div class="flex justify-end">
    <div class="fixed bottom-0 z-20 p-6">
        <x-filament::modal id="chat" class="flex justify-end">
            <x-slot name="trigger">
                <x-filament::icon-button
                        class="border-2"
                        icon="heroicon-c-question-mark-circle"
                        label="New label"
                        size="xl"
                />
            </x-slot>

            <x-slot name="heading">
                <div class="flex flex-col space-y-1.5 pb-6">
                    <h2 class="font-semibold text-lg tracking-tight">Bantuan</h2>
                    <p class="text-sm text-[#6b7280] leading-3">Powered by Zehamless</p>
                </div>
            </x-slot>

            <!-- Chatbot Container -->
            <div class="w-80 h-96 overflow-y-auto" id="chat-container" x-ref="chatContainer">
                <!-- Chat Message AI -->
                <div class="flex gap-3 my-4 text-gray-600 text-sm flex-1">
                    <span class="relative flex shrink-0 overflow-hidden rounded-full w-8 h-8">
                        <div class="rounded-full bg-gray-100 border p-1">
                            <svg stroke="none" fill="black" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"
                                 height="20" width="20" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"></path>
                            </svg>
                        </div>
                    </span>
                    <p class="leading-relaxed bg-gray-200 p-2 rounded">
                        <span class="block font-bold text-gray-700">Bot </span>Halo, ada yang bisa saya bantu?
                    </p>
                </div>

                @foreach($conversation as $text)
                    @if($text['sender'] == auth()->id())
                        <!-- User Chat Message -->
                        <div class="flex gap-3 my-4 text-gray-600 text-sm flex-1 justify-end">
                            <p class="leading-relaxed text-right bg-gray-200 p-2 rounded">
                                <span class="block font-bold text-gray-700">Anda </span>{{$text['question']}}
                            </p>
                            <span class="relative flex shrink-0 overflow-hidden rounded-full w-8 h-8">
                                <div class="rounded-full bg-gray-100 border p-1">
                                    <svg stroke="none" fill="black" stroke-width="0" viewBox="0 0 16 16" height="20"
                                         width="20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"></path>
                                    </svg>
                                </div>
                            </span>
                        </div>
                    @else
                        <!-- AI Chat Message -->
                        <div class="flex gap-3 my-4 text-gray-600 text-sm flex-1">
    <span class="relative flex shrink-0 overflow-hidden rounded-full w-8 h-8">
        <div class="rounded-full bg-gray-100 border p-1">
            <svg stroke="none" fill="black" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true" height="20"
                 width="20" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"></path>
            </svg>
        </div>
    </span>
                            <div class="bg-gray-200 p-2 rounded leading-relaxed">
                                <span class="block font-bold text-gray-700">Bot</span>
                                <div class="leading-relaxed markdown">
                                    {!! \Illuminate\Support\Str::markdown($text['answer']) !!}
                                </div>
                            </div>
                        </div>

                    @endif
                @endforeach
            </div>
            <form wire:submit="save" class="flex items-center justify-center w-full gap-2">
                @csrf
                <x-filament::input.wrapper :is-valid="! $errors->has('message')">
                    <x-filament::input
                        required
                            type="text"
                            wire:model="message"
                            placeholder="Type your message here..."
                    />
                </x-filament::input.wrapper>
                <x-filament::button type="submit">
                    <span wire:loading.remove>Send</span>
                    <x-filament::loading-indicator class="h-5 w-5" wire:loading/>
                </x-filament::button>
            </form>
        </x-filament::modal>
    </div>
</div>

@script
<script>
    Livewire.on('scroll-to-bottom', () => {
        setTimeout(() => {
            const chatContainer = document.querySelector('#chat-container');
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }, 100); // Delay for 100ms to allow DOM updates
    });
</script>
@endscript
