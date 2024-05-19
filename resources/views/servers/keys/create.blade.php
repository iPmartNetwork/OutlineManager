@extends('layouts.app')

@section('content')
    <section class="mt-5 px-1 px-lg-5">
        <div class="d-flex align-items-center gap-2">
            <a class="btn btn-tool" href="{{ route('servers.keys.index', $server->id) }}">
                <x-svg.arrow-left width="20" height="20" />
            </a>
            <h2>{{ __('New key') }}</h2>
        </div>

        <form action="{{ route('servers.keys.store', $server->id) }}" method="post">
            @csrf

            <section class="d-grid gap-3 my-3">
                <section>
                    <label for="name" class="ps-1 mb-1">{{ __('Name') }}</label>
                    <input class="d-block" id="name" name="name" required value="{{ old('name') }}" autofocus>
                    @error('name')<small class="ps-1 error-message">{{ $message }}</small>@enderror
                </section>

                <section>
                    <label for="data_limit" class="ps-1 mb-1">{{ __('Data limit') }}</label>
                    <input class="d-block" id="data_limit" type="number" name="data_limit" value="{{ old('data_limit') }}" placeholder="Data limit in bytes">
                    @error('data_limit')<small class="ps-1 error-message">{{ $message }}</small>@enderror
                </section>

                <section>
                    <label for="expires_at" class="ps-1 mb-1">{{ __('Expiration date') }}</label>
                    <input class="d-block" id="expires_at" type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" min="{{ now()->format('Y-m-d\TH:i') }}">
                    @error('expires_at')<small class="ps-1 error-message">{{ $message }}</small>@enderror
                </section>
            </section>

            <button class="btn btn-primary">{{ __('Create') }}</button>
        </form>
    </section>
@endsection
