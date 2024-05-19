<header>
    <nav class="d-flex justify-content-between flex-wrap">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('servers.index') }}">
            <x-svg.app />
            <strong class="text-uppercase">{{ config('app.name') }}</strong>
        </a>

        <section class="d-flex align-items-center gap-3">
            <a  class="btn btn-tool" href="{{ config('app.github_url') }}" target="_blank">
                <x-svg.github width="24" height="24" />
            </a>

            @auth
                <form action="{{ route('auth.login.destroy') }}" method="post">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-tool" title="{{ __('Logout') }}">
                        <x-svg.logout width="24" height="24" />
                    </button>
                </form>
            @endauth
        </section>
    </nav>
</header>

