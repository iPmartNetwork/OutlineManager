<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ServerController extends Controller
{
    public function index(Request $request)
    {
        $numberOfServers = Server::count();
        $servers = Server::when($request->has('q'), function ($query) use ($request) {
            $query->where('name', 'LIKE', "%$request->q%")
                ->orWhere('hostname_or_ip', 'LIKE', "%$request->q%");
        })->latest()->get();

        return view('servers.index', compact('servers', 'numberOfServers'));
    }

    public function create()
    {
        return view('servers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'api_url_and_cert_sha256' => 'required|max:255',
        ]);

        $data = json_decode($request->api_url_and_cert_sha256);

        try {
            $parsedUrl = parse_url($data->apiUrl);

            Server::create([
                'api_url' => $data->apiUrl,
                'api_cert_sha256' => $data->certSha256,
                'hostname_or_ip' => $parsedUrl['host'],
            ]);
        } catch (Throwable $exception) {
            // TODO: report to sentry
            return back()->withInput()->withErrors(['api_url_and_cert_sha256' => __('Could not create new server. Make sure your API URL is correct.')]);
        }

        return redirect()->route('servers.index');
    }

    public function edit(Server $server)
    {
        return view('servers.edit', compact('server'));
    }

    public function update(Request $request, Server $server)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'hostname_for_new_access_keys' => 'required|max:128',
            'port_for_new_access_keys' => 'required|integer|min:1|max:65535',
        ]);

        DB::transaction(function () use ($validatedData, $server) {
            $server->update($validatedData);
        });

        return redirect()->route('servers.keys.index', $server->id);
    }

    public function destroy(int $server)
    {
        Server::whereId($server)->delete();

        return redirect()->route('servers.index');
    }
}
