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
<div id="root" class="font-sans bg-white antialiased min-h-screen flex flex-col justify-center">
    <div class="flex flex-1 flex-col min-h-screen items-center justify-center bg-pattern-topography border-t-4 border-blue">
        <div class="w-auto max-w-sm px-3 py-6 flex flex-col text-center">
            <div class="mb-3">
                <img src="{{ $page->baseUrl }}/img/mum.svg" class="border border-grey-lighter rounded-full w-24 h-24 shadow-md">
            </div>
            <div class="font-extrabold text-5xl">MUM</div>
            <div class="mt-4 text-grey-dark">{{ $page->version }}</div>
            <div class="mt-8 text-2xl text-grey-darkest leading-normal">A web-based user management tool for Postfix and
                Dovecot that is easy to use and still very powerful.
            </div>
            <div class="mt-12 flex flex-row flex-wrap justify-center -mx-6">
                <a href="{{ $page->baseUrl }}/what-is-mum" class="btn-xl">
                    Docs
                </a>
                <a href="https://github.com/mum-project/mum" target="_blank" rel="noreferrer noopener" class="btn-xl bg-white border-grey-lightest text-blue">
                    GitHub
                </a>
            </div>
        </div>
    </div>
    <div class="flex flex-1 flex-col py-16 px-3 items-center border-t border-grey-lighter">
        <div class="uppercase tracking-wide text-sm text-grey-dark font-bold">A project by</div>
        <div class="mt-6 -mx-8 lg:-mx-16 flex flex-row flex-wrap items-center">
            <div class="flex flex-col mx-8 lg:mx-16 w-32 items-center">
                <img src="https://res.cloudinary.com/dwpkvglev/image/upload/c_crop,g_auto:face,w_1609/v1532871661/martbock.jpg" class="w-16 h-16 bg-grey-lighter rounded-full"/>
                <div class="mt-4">
                    Martin Bock
                </div>
                <a href="https://twitter.com/martbock" target="_blank" rel="noreferrer noopener" class="mt-2 text-sm text-blue-dark no-underline hover:text-blue-light  focus:text-blue-light trans-all">
                    @martbock
                </a>
            </div>
            <div class="flex flex-col mx-8 lg:mx-16 w-32 items-center">
                <img src="https://res.cloudinary.com/dwpkvglev/image/upload/v1532871717/mrmaxmerz.jpg" class="w-16 h-16 bg-grey-lighter rounded-full"/>
                <div class="mt-4">
                    Max Merz
                </div>
                <a href="https://twitter.com/MrMaxMerz" target="_blank" rel="noreferrer noopener" class="mt-2 text-sm text-blue-dark no-underline hover:text-blue-light focus:text-blue-light  trans-all">
                    @MrMaxMerz
                </a>
            </div>
        </div>
    </div>
</div>
<script src="{{ $page->baseUrl . mix('js/main.js') }}"></script>
</body>
</html>
