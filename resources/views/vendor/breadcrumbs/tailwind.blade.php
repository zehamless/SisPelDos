@unless ($breadcrumbs->isEmpty())
    <nav class="container mx-auto breadcrumbs">
        <ul class="p-4 rounded flex flex-wrap text-sm text-gray-800">
            @foreach ($breadcrumbs as $breadcrumb)

                @if ($breadcrumb->url && !$loop->last)
                    <li>
                        <a href="{{ $breadcrumb->url }}" class="hover:text-teal-700">
                            {{ $breadcrumb->title }}
                        </a>
                    </li>
                @else
                    <li>
                        {{ $breadcrumb->title }}
                    </li>
                @endif

{{--                @unless($loop->last)--}}
{{--                    <li class="text-gray-500 px-2">--}}
{{--                        /--}}
{{--                    </li>--}}
{{--                @endif--}}

            @endforeach
        </ul>
    </nav>
@endunless
