<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" id="register-form">
        @csrf

        <div class="mt-4">
            <label for="my_modal_6" class="btn w-full">{{__('Search')}} Dosen</label>

            <!-- Put this part before </body> tag -->
            <input type="checkbox" id="my_modal_6" class="modal-toggle"/>
            <div class="modal" role="dialog">
                <div class="modal-box">
                    <h3 class="font-bold text-lg">{{__('Search')}} Dosen</h3>
                    <div class="flex flex-row py-4">
                        <input type="text" placeholder="Type here" class="input input-bordered w-full" id="data_dosen" required/>
                        <button class="btn mx-3" id="buttonSearchDosen">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="lucide lucide-search">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.3-4.3"/>
                            </svg>
                        </button>
                    </div>
                    <div class="overflow-y-auto grid grid-cols-1 gap-1" id="listDosen">

                    </div>

                    <div class="modal-action">
                        <label for="my_modal_6" class="btn" id="closeModal">Close!</label>
                    </div>
                </div>
            </div>
        </div>

            <div id="selectedDosen" class="mt-4 mb-4 grid grid-cols-1">
        </div>
            <hr>
        {{--        <!-- Name -->--}}
        {{--        <div>--}}
        {{--            <x-input-label for="name" :value="__('Name')" />--}}
        {{--            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />--}}
        {{--            <x-input-error :messages="$errors->get('name')" class="mt-2" />--}}
        {{--        </div>--}}

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                          autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>
        <x-text-input id="no_induk" name="no_induk" hidden/>
        <x-text-input id="nama" name="nama" hidden/>
        <x-text-input id="universitas" name="universitas" hidden/>
        <x-text-input id="prodi" name="prodi" hidden/>
        <x-text-input id="link" name="link" hidden/>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')"/>

            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')"/>

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password"/>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
               href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4" id="registerButton">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
    @push('scripts')
        <script !src="">
            const getDosenRoute = "{{route('get-list-dosen')}}";
            const registerRoute = "{{route('register')}}";
            const getDataRoute = "{{route('get-dosen-data')}}";
            const sendForm = false;
        </script>
        <script src="{{asset('js/register.js')}}"></script>
    @endpush
</x-guest-layout>

