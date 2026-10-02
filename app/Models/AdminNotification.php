<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    protected $fillable = ['admin_id', 'type', 'icon', 'title', 'body', 'data', 'read_at'];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public static function forAdmin(int $adminId, string $type, string $title, string $body, array $data = []): self
    {
        $iconMap = [
            'support'  => 'comments',
            'transfer' => 'exchange-alt',
            'account'  => 'user-plus',
            'system'   => 'bell',
        ];

        return self::create([
            'admin_id' => $adminId,
            'type'     => $type,
            'icon'     => $iconMap[$type] ?? 'bell',
            'title'    => $title,
            'body'     => $body,
            'data'     => $data ?: null,
        ]);
    }

    /**
     * IDs des admins à notifier pour ce client : uniquement l'admin responsable
     * (created_by, ou à défaut l'admin du dossier de prêt) + systématiquement
     * tous les super-admins — jamais l'ensemble des admins.
     */
    public static function recipientAdminIds(User $client): \Illuminate\Support\Collection
    {
        $ids = collect();

        if ($client->created_by) {
            $ids->push($client->created_by);
        } else {
            $loanAdminId = $client->clientLoans()->whereNotNull('admin_id')->value('admin_id');
            if ($loanAdminId) {
                $ids->push($loanAdminId);
            }
        }

        $ids = $ids->merge(User::role('super-admin')->pluck('id'));

        return $ids->filter()->unique()->values();
    }
}
