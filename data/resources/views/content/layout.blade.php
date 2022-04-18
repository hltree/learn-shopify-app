@include('header')
<style>
    img {
        width: 100%;
    }

    a {
        color: blue;
        text-decoration: underline;
    }

    a:hover {
        text-decoration: none;
    }
</style>
<div class="lg:p-32 md:p-16 sm:p-8 min-w-0 w-full flex-auto lg:static lg:max-h-full lg:overflow-visible">
    <div class="w-full flex">
        <div class="min-w-0 flex-auto px-4 sm:px-6 xl:px-8 pt-10 pb-24 lg:pb-16"><h1
                class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $title }}</h1>
            @isset ($lead)
                <p class="mt-1 text-lg font-semibold text-green-400">{!! $lead !!}</p>
            @endisset
            @isset ($sections)
                @if (is_array($sections))
                    @foreach ($sections as $section)
                        <hr class="my-5">
                        @isset($section['title'])
                            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{!! $section['title'] !!}</h2>
                        @endisset
                        @isset($section['content'])
                            <p class="mt-1 text-base">{!! $section['content'] !!}</p>
                        @endisset
                    @endforeach
                @endif
            @endisset
        </div>
    </div>
</div>
@include('footer')
