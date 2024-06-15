<div class="flex flex-col items-center">
    <img
        src="{{ asset(auth()->user()->picture ? 'storage/' . auth()->user()->picture : 'assets/defaultProfile.jpg') }}"
        class="rounded-lg" style="max-width: 150px;">
    <div class="text-center">
        {{auth()->user()->nama}}
    </div>
</div>
