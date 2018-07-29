<div class="text-grey-darker md:w-64 flex flex-col z-20 relative h-full">
    <div class="sticky pin-t flex flex-col md:overflow-y-auto md:w-64 truncate">
        @foreach($page->navigation as $sectionTitle => $sectionLinks)
            <div class="mt-6 mb-2 px-6 text-grey uppercase tracking-wide font-bold text-xs">{{ $sectionTitle }}</div>
            @foreach($sectionLinks as $name => $link)
                <a class="{{ $page->active($link) ? 'text-blue-dark font-semibold' : 'text-grey-darker' }} px-6 mb-1 py-1 text-sm no-underline hover:text-black focus:text-black truncate"
                   href="{{ $page->baseUrl }}/{{ $link }}"
                >
                    {{ $name }}
                </a>
            @endforeach
        @endforeach
    </div>
</div>