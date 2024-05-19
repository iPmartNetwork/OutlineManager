@extends('layouts.app')

@section('content')
    <section class="mt-5 px-1 px-lg-5">
        <header class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-tool" href="{{ route('servers.keys.index', $server->id) }}">
                    <x-svg.arrow-left width="20" height="20" />
                </a>
                <h2>{{ __('Editable information') }}</h2>
            </div>
        </header>

        <form class="d-grid gap-3 mt-3" action="{{ route('servers.update', $server->id) }}" method="post">
            @csrf
            @method('PATCH')
            <section>
                <label for="name" class="ps-1 mb-1">{{ __('Name') }}</label>
                <input class="d-block" id="name" name="name" required value="{{ old('name', $server->name) }}" autofocus>
                <small class="ps-1 d-block text-muted">{{ __('Set a new name for your server. Note that this will not be reflected on the devices of the users that you invited to connect to it.') }}</small>
                @error('name')<small class="ps-1 error-message">{{ $message }}</small>@enderror
            </section>

            <section>
                <label for="hostname" class="ps-1 mb-1">{{ __('Hostname or IP for new access keys') }}</label>
                <input class="d-block" id="hostname" name="hostname_for_new_access_keys" required value="{{ old('hostname_for_new_access_keys', $server->hostname_for_new_access_keys) }}">
                <small class="ps-1 d-block text-muted">{{ __('This will not affect the existing access keys.') }}</small>
                @error('hostname_for_new_access_keys')<small class="ps-1 error-message">{{ $message }}</small>@enderror
            </section>

            <section>
                <label for="port" class="ps-1 mb-1">{{ __('Port for new access keys (Max: 65535)') }}</label>
                <input class="d-block" type="number" id="port" name="port_for_new_access_keys" required value="{{ old('port_for_new_access_keys', $server->port_for_new_access_keys) }}">
                <small class="ps-1 d-block text-muted">{{ __('This will not affect the existing access keys.') }}</small>
                @error('port_for_new_access_keys')<small class="ps-1 error-message">{{ $message }}</small>@enderror
            </section>

            <section class="d-flex justify-content-between">
                <button class="btn btn-primary">{{ __('Update') }}</button>
            </section>
        </form>

        <section class="mt-5">
            <h2>{{ __('Remove this server') }}</h2>
            <p class="mb-3">{{ __("Please note that this action will only remove the server from the :app's database. The server itself will not be affected.", ['app' => config('app.name')]) }}</p>

            <form action="{{ route('servers.destroy', $server->id) }}" method="post" onsubmit="return confirm('{{ __('Are you sure you want to remove this server?') }}')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">{{ __('Remove') }}</button>
            </form>
        </section>
    </section>


@endsection
