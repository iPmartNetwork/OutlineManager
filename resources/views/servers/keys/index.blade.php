@extends('layouts.app')

@section('content')
    <div class="mt-5 px-1 px-lg-5">
        <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-tool" href="{{ route('servers.index') }}">
                        <x-svg.arrow-left width="20" height="20" />
                    </a>
                    <h2>{{ $server->name }}</h2>
                </div>

                <a class="btn" href="{{ route('servers.edit', $server->id) }}">{{ __('Settings') }}</a>
            </div>

            <section class="row card text-wrap mb-3 mx-1 mx-md-auto">
                <div class="col-12 col-md-6 mb-1">
                    <span class="opacity-75">{{ __('Status') }}:</span>

                    @if ($server->is_available)
                        <span class="status status-success d-inline-block">{{ __('Available') }}</span>
                    @else
                        <span class="status status-danger d-inline-block">{{ __('Not Available') }}</span>
                    @endif
                </div>

                <div class="col-12 col-md-6 mb-1">
                    <span class="opacity-75">{{ __('Version') }}:</span>
                    <span class="status status-secondary d-inline-block">{{ $server->version }}</span>
                </div>

                <div class="col-12 col-md-6 mb-1">
                    <span class="opacity-75">{{ __('Creation date') }}:</span>
                    <span class="status status-secondary d-inline-block">{{ $server->api_created_at }} ({{ $server->api_created_at->diffForHumans() }})</span>
                </div>

                <div class="col-12 col-md-6 mb-1">
                    <span class="opacity-75">{{ __('Number of keys') }}:</span>
                    <span class="status status-secondary d-inline-block">{{ $server->keys_count }}</span>
                </div>

                <div class="col-12 col-md-6 mb-1">
                    <span class="opacity-75">{{ __('Total usage') }}:</span>
                    <span class="status status-secondary d-inline-block">{{ format_bytes($server->total_data_usage) }}</span>
                </div>

                <div class="col-12 col-md-6 mb-1">
                    <span class="opacity-75">{{ __('Port for new access keys') }}</span>
                    <span class="status status-secondary d-inline-block">{{ $server->port_for_new_access_keys }}</span>
                </div>

                <div class="col-12 col-md-6 mb-1">
                    <span class="opacity-75">{{ __('Hostname for new access keys') }}</span>
                    <span class="status status-secondary d-inline-block">{{ $server->hostname_for_new_access_keys }}</span>
                </div>

                <div class="col-12">
                    <span class="opacity-75">{{ __('Management API URL') }}:</span>
                    <a class="status status-secondary d-inline-flex gap-1 align-items-center" href="{{ $server->api_url }}" target="_blank">
                        <span>{{ $server->api_url }}</span>
                        <x-svg.open-in-new width="16" height="16" />
                    </a>
                </div>
            </section>
        </div>

        <div>
            <div class="d-flex justify-content-between gap-2 align-items-center mb-3">
                <h2>{{ __('🗝️ Access Keys') }}</h2>

               @if ($server->is_available)
                    <a href="{{ route('servers.keys.create', $server->id) }}" class="btn btn-primary">{{ __('Create') }}</a>
               @endif
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                    <tr class="text-uppercase">
                        <th class="d-none d-lg-table-cell">{{ __('#') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Data Usage') }}</th>
                        <th>{{ __('Validity') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($keys as $key)
                        <tr data-key="{{ $key->access_url . '#' . rawurlencode($key->name) }}">
                            <td class="d-none d-lg-table-cell">{{ $loop->index + 1 }}</td>
                            <td><span class="d-inline-block w-max mx-auto">{{ $key->name }}</span></td>
                            <td>
                                <div class="w-max mx-auto d-flex align-items-center gap-1 justify-content-center">
                                    <span>{{ format_bytes($key->data_usage) }}</span>
                                    <span class="opacity-50">{{ __('of') }}</span>
                                    <span class="d-flex align-items-center">
                                        @if ($key->data_limit)
                                            {{format_bytes($key->data_limit)}}
                                        @else
                                            <x-svg.infinity width="24" height="24" />
                                        @endif
                                    </span>
                                </div>
                            <td>
                                <div class="w-max mx-auto d-flex justify-content-center">
                                    @if ($key->expires_at)
                                        @if ($key->is_expired)
                                            <span class="status status-danger">{{ __('Expired') }}</span>
                                        @else
                                            <span class="status status-warning countdown" data-expired-label="{{ __('Expired') }}" data-value="{{ $key->expires_at->timestamp }}" title="{{ __('Until :datetime', ['datetime' => $key->expires_at]) }}">
                                                {{ format_as_duration(now(), $key->expires_at) }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="status status-success d-flex align-items-center" title="{{ __('Forever') }}">
                                            <x-svg.infinity width="24" height="24" />
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="w-max mx-auto d-flex gap-2 align-items-center justify-content-center flex-wrap">
                                    <button class="btn btn-tool" title="{{ __('Show access key') }}" data-dialog-trigger="true" data-dialog="accessKeyModal" data-as-modal="true" onclick="bindAccessKey(this)">
                                        <x-svg.key width="20" height="20" />
                                    </button>

                                    <button class="btn btn-tool" title="{{ __('Show QR code') }}" data-dialog-trigger="true" data-dialog="qrCodeModal" data-as-modal="true" onclick="createQRCode(this)">
                                        <x-svg.qr-code width="20" height="20" />
                                    </button>

                                    <button class="btn btn-tool" title="{{ __('Copy access key to clipboard') }}" onclick="copyToClipboard('{{ $key->access_url . '#' . rawurlencode($key->name) }}', '{{ __('Copied 😎') }}')">
                                        <x-svg.copy width="20" height="20" />
                                    </button>

                                    @if ($server->is_available)
                                        <a href="{{ route('servers.keys.edit', [$server->id, $key->id]) }}" class="btn btn-tool" title="{{ __('Edit the key') }}">
                                            <x-svg.edit width="20" height="20" />
                                        </a>
                                        <form method="post" action="{{ route('servers.keys.destroy', [$server->id, $key->id]) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-tool btn-danger" title="{{ __('Remove the key') }}">
                                                <x-svg.delete width="20" height="20" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="99">
                                <section class="p-3 text-center text-muted d-grid gap-2 align-items-center">
                                    <div>¯\_(ツ)_/¯</div>
                                    <div>{{ __("There is no access key to display!") }}</div>
                                </section>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 mb-5">
                {{ $keys->links() }}
            </div>
        </div>

        <dialog id="accessKeyModal">
            <h3>{{ __('Access Key URL') }}</h3>
            <div class="my-3">
                <code id="accessKeyModalValue">__access_key__</code>
            </div>
            <div class="text-end">
                <button class="btn px-4" data-dialog-close="true" data-dialog="accessKeyModal">{{ __('Ok') }}</button>
            </div>
        </dialog>

        <dialog id="qrCodeModal">
            <h3>{{ __('️Access Key QR Code') }}</h3>
            <div class="my-3" id="accessKeyQRCodeContainer"></div>
            <div class="text-end">
                <button class="btn px-4" data-dialog-close="true" data-dialog="qrCodeModal">{{ __('Ok') }}</button>
            </div>
        </dialog>
    </div>
@endsection

@section('footer-stuff')
    <script>
        const accessKeyModalValueEl = document.getElementById('accessKeyModalValue');

        const bindAccessKey = (el) => {
            accessKeyModalValueEl.innerHTML = el.closest('tr').dataset.key;
        }

        const createQRCode = (el) => {
            const intervalId = setInterval(() => {
                if (!window.createQRCode)
                    return;

                clearInterval(intervalId);
                window.createQRCode(el.closest('tr').dataset.key, '{{ asset('favicon.svg') }}', '#accessKeyQRCodeContainer');
            }, 100);
        }
    </script>
@endsection
