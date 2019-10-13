<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ $page->baseUrl . mix('css/main.css') }}">
    <title>@yield('title', 'MUM Docs')</title>
</head>
<body>
<div id="root" class="font-sans bg-white antialiased min-h-screen flex flex-col md:flex-row justify-center">
    <div class="w-full absolute bg-white pin-t pin-x z-30 h-16 border-b border-grey-lighter">
        <div class="flex flex-row items-center justify-between h-16 max-w-4xl px-2 w-full mx-auto">
            <div class="flex flex-row items-center text-grey-darker">
                <a href="{{ $page->baseUrl }}" class="mx-4 text-xl font-extrabold text-grey-dark no-underline hover:text-grey-darkest focus:text-grey-darkest">MUM</a>
                <div class="mx-4 text-grey-dark">{{ $page->version }}</div>
            </div>
            <div class="flex flex-row items-center mx-4 text-grey-darker">
                <a href="https://github.com/mum-project/mum" target="_blank" rel="noreferrer noopener" class="mx-4 text-grey-dark hover:text-grey-darker focus:text-grey-darker">
                    <svg class="fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>GitHub</title>
                        <path d="M10 0a10 10 0 0 0-3.16 19.49c.5.1.68-.22.68-.48l-.01-1.7c-2.78.6-3.37-1.34-3.37-1.34-.46-1.16-1.11-1.47-1.11-1.47-.9-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.9 1.52 2.34 1.08 2.91.83.1-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.94 0-1.1.39-1.99 1.03-2.69a3.6 3.6 0 0 1 .1-2.64s.84-.27 2.75 1.02a9.58 9.58 0 0 1 5 0c1.91-1.3 2.75-1.02 2.75-1.02.55 1.37.2 2.4.1 2.64.64.7 1.03 1.6 1.03 2.69 0 3.84-2.34 4.68-4.57 4.93.36.31.68.92.68 1.85l-.01 2.75c0 .26.18.58.69.48A10 10 0 0 0 10 0"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <div class="pt-16 flex flex-col min-h-screen md:flex-row w-full max-w-4xl justify-center z-20">
        @include('_partials/sidebar')
        <div class="sm:px-3 flex flex-col flex-grow items-center z-10">
            <div class="markdown px-6 py-2 mb-8 w-full flex-grow max-w-lg">
                <div class="mt-6 mb-6">
                    <h1 class="font-extrabold mb-3">{{ $page->title }}</h1>
                    @if ($page->description)
                        <div class="text-xl text-grey-dark">
                            {{ $page->description }}
                        </div>
                    @endif
                </div>
                @yield('content')
            </div>
        </div>
        @include('_partials/table_of_contents')
    </div>
</div>
<script src="{{ $page->baseUrl }}/assets/js/prism.js"></script>
<script src="{{ $page->baseUrl . mix('js/main.js') }}"></script>
</body>
</html>
