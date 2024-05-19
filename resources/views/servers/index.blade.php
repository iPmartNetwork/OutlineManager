@extends('layouts.app')

@section('footer-stuff')
    <script>
        const searchInput = document.getElementById('serverSearchInput');
        searchInput?.addEventListener('search', () => {
            searchInput.closest('form').submit();
        });
    </script>
@endsection

@section('content')
    <section class="mt-5 px-1 px-lg-5">
        <header class="mb-3">
            <h2>{{ __('Your Servers') }}</h2>
        </header>

        @if ($numberOfServers > 0)
            <section class="d-flex justify-content-between align-items-center mb-3">
                <search>
                    <form action="{{ route('servers.index') }}" class="d-flex justify-content-between align-items-center">
                        <input id="serverSearchInput" autofocus type="search" name="q" value="{{ request()->input('q') }}" placeholder="{{ __('🔍 Name or IP [+Enter]') }}" size="28" />
                    </form>
                </search>

                <a href="{{ route('servers.create') }}" class="btn btn-primary">{{ __('Add') }} </a>
            </section>
        @endif

        @if ($numberOfServers === 0)
            <section class="p-3 text-center mt-5">
                <p>{{ __("You haven't added any server yet!") }}</p>

                <a href="{{ route('servers.create') }}" class="btn btn-primary mt-3">{{ __('Add first one') }}</a>
            </section>

        @else
            <div class="table-wrapper">
                <table>
                    <thead>
                    <tr class="text-uppercase">
                        <th class="d-none d-lg-table-cell">{{ __('#') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Hostname or IP') }}</th>
                        <th>{{ __('Number of Keys') }}</th>
                        <th>{{ __('Total Usage') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($servers as $server)
                        <tr>
                            <td class="d-none d-lg-table-cell">{{ $loop->index + 1 }}</td>
                            <td><span class="d-inline-block w-max mx-auto">{{ $server->name }}</span></td>
                            <td><span class="d-inline-block w-max mx-auto">{{ $server->hostname_or_ip }}</span></td>
                            <td><span class="d-inline-block w-max mx-auto status status-secondary">{{ $server->keys()->count() }}</span></td>
                            <td><span class="d-inline-block w-max mx-auto status status-secondary">{{ format_bytes($server->total_data_usage) }}</span></td>
                            <td>
                                @if ($server->is_available)
                                    <span class="d-inline-block w-max mx-auto status status-success">{{ __('Available') }}</span>
                                @else
                                    <span class="d-inline-block w-max mx-auto status status-danger">{{ __('Not Available') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('servers.keys.index', $server->id) }}" class="btn w-max mx-auto">{{ __('Manage') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="99">
                                <section class="p-3 text-center text-muted d-grid gap-2 align-items-center">
                                    <div>¯\_(ツ)_/¯</div>
                                    <div>{{ __("No Result!") }}</div>
                                </section>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
