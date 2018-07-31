<div class="rounded my-3 overflow-hidden bg-grey-lightest">
    <div class="bg-grey-dark text-white px-4 py-2 text-sm text-right font-mono">
        {{ $filename }}
    </div>
    <pre class="m-0 p-4 language-{{ $lang ?? 'html' }}"><code>{{ e($slot) }}</code></pre>
</div>