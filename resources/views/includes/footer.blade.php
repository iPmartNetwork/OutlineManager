<footer>
    <section class="d-flex align-items-center justify-content-center gap-2 my-3 text-muted">
        <span>{{ __('Made with') }}</span>
        <span class="mt-1">
            <x-svg.heart width="20" height="20" />
        </span>
        <span>{{ __('by') }}</span>
        <span  class="mt-1">
            <a href="{{ config('app.author_url') }}" title="{{ config('app.author_name') }}" target="_blank">
                <x-svg.amro045 width="24" height="24" />
            </a>
        </span>
        <span>{{ __('for FREE internet') }}</span>
    </section>
</footer>
