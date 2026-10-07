<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\ClientNotification;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
// str_starts_with() used directly (PHP 8+)

class SupportController extends Controller
{
    public function index()
    {
        $clients      = $this->clientsForSidebar();
        $isSuperAdmin = Auth::user()->hasRole('super-admin');

        return view('admin.support.index', compact('clients', 'isSuperAdmin'));
    }

    public function show(User $client)
    {
        $this->authorizeClient($client);

        $messages = SupportMessage::where('client_id', $client->id)->oldest()->get();

        SupportMessage::where('client_id', $client->id)
            ->where('sender_type', 'client')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        AdminNotification::where('admin_id', Auth::id())
            ->where('type', 'support')
            ->whereJsonContains('data->client_id', $client->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $lastId   = $messages->last()?->id ?? 0;
        $chatData = $messages->map(fn ($m) => $m->toChat());
        $clients  = $this->clientsForSidebar();

        return view('admin.support.show', compact('client', 'chatData', 'lastId', 'clients'));
    }

    public function poll(Request $request, User $client)
    {
        $this->authorizeClient($client);
        $after = (int) $request->query('after', 0);

        $messages = SupportMessage::where('client_id', $client->id)
            ->where('id', '>', $after)
            ->oldest()
            ->get();

        $messages->where('sender_type', 'client')
            ->each(fn ($m) => $m->update(['read_at' => now()]));

        return response()->json([
            'messages' => $messages->map(fn ($m) => $m->toChat()),
        ]);
    }

    public function store(Request $request, User $client)
    {
        $this->authorizeClient($client);
        $request->validate([
            'body' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:10240',
        ]);

        if (! $request->filled('body') && ! $request->hasFile('file')) {
            return response()->json(['error' => 'Message vide'], 422);
        }

        $filePath = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $mime     = $file->getMimeType();
            $fileType = str_starts_with($mime, 'image/') ? 'image' : 'audio';
            $filePath = $file->store("support/{$client->id}", 'public');
        }

        $msg = SupportMessage::create([
            'client_id'   => $client->id,
            'sender_type' => 'admin',
            'body'        => $request->body,
            'file_path'   => $filePath,
            'file_type'   => $fileType,
        ]);

        $preview = $msg->body
            ? Str::limit($msg->body, 80)
            : ($msg->file_type === 'image' ? '📷 Image' : '🎤 Audio');

        ClientNotification::forUser(
            $client->id,
            'system',
            __('app.notif_support_reply', [], $client->locale ?? 'fr'),
            $preview,
            ['type' => 'support']
        );

        return response()->json(['message' => $msg->toChat()]);
    }

    private function authorizeClient(User $client): void
    {
        $authUser = Auth::user();
        if ($authUser->hasRole('super-admin')) return;

        $hasAccess = $client->created_by === $authUser->id;
        abort_unless($hasAccess, 403, 'Accès non autorisé.');
    }

    private function clientsForSidebar(): \Illuminate\Support\Collection
    {
        $authUser     = Auth::user();
        $isSuperAdmin = $authUser->hasRole('super-admin');

        return User::where('type', 'client')
            ->whereHas('supportMessages')
            ->when(! $isSuperAdmin, fn ($q) => $q->where(fn ($q2) => $q2
                ->where('created_by', $authUser->id)
            ))
            ->with(['supportMessages' => fn ($q) => $q->latest()->limit(1)])
            ->get()
            ->map(function (User $c) {
                $c->last_message     = $c->supportMessages->first();
                $c->unread_for_admin = SupportMessage::where('client_id', $c->id)
                    ->where('sender_type', 'client')
                    ->whereNull('read_at')
                    ->count();
                return $c;
            })
            ->sortByDesc(fn ($c) => optional($c->last_message)->created_at);
    }
}
