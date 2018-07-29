<div class="py-3 pl-4 pr-4 my-4 bg-red-light text-white rounded flex flex-row items-start">
    <div class="text-white mr-4">
        <svg class="w-8 h-8 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path class="heroicon-ui"
                  d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20zm0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm0 9a1 1 0 0 1-1-1V8a1 1 0 0 1 2 0v4a1 1 0 0 1-1 1zm0 4a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
        </svg>
    </div>
    <div class="leading-normal">
        {!! $slot !!}
    </div>
</div>