<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ContractTemplate;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'type', 'gender', 'created_by', 'invitation_token',
        'phone', 'address', 'birth_date', 'id_type', 'id_number', 'date_delivre', 'tax_number', 'activity', 'currency', 'locale', 'balance',
        'bank_account', 'bic',
        'is_blocked', 'unblock_token', 'unblock_token_expires_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'        => 'datetime',
        'birth_date'               => 'date',
        'date_delivre'             => 'date',
        'balance'                  => 'decimal:2',
        'is_blocked'               => 'boolean',
        'unblock_token_expires_at' => 'datetime',
        'bank_account'             => \App\Casts\SafeEncrypted::class,
        'bic'                      => \App\Casts\SafeEncrypted::class,
        'id_number'                => \App\Casts\SafeEncrypted::class,
        'tax_number'               => \App\Casts\SafeEncrypted::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->uuid = $user->uuid ?? (string) \Illuminate\Support\Str::uuid();
        });
    }

    // Clé utilisée pour le routage HTTP ({user}) — non devinable, distincte de l'id interne
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Le personnel (admin/super-admin) reçoit un email de réinitialisation dédié,
    // avec un lien vers l'espace staff plutôt que l'espace client.
    public function sendPasswordResetNotification($token): void
    {
        if ($this->type === 'staff') {
            $this->notify(new \App\Notifications\StaffResetPasswordNotification($token));
            return;
        }

        parent::sendPasswordResetNotification($token);
    }

    // Demandes créées par cet admin
    public function createdLoans()
    {
        return $this->hasMany(LoanRequest::class, 'admin_id');
    }

    // Demandes dont cet utilisateur est le client
    public function clientLoans()
    {
        return $this->hasMany(LoanRequest::class, 'client_id');
    }

    // Messages de support (côté client)
    public function supportMessages()
    {
        return $this->hasMany(SupportMessage::class, 'client_id');
    }

    // Notifications admin
    public function adminNotifications()
    {
        return $this->hasMany(AdminNotification::class, 'admin_id');
    }

    // Modèles de contrats attribués à cet admin
    public function assignedTemplates()
    {
        return $this->belongsToMany(
            ContractTemplate::class,
            'admin_contract_template',
            'admin_id',
            'contract_template_id'
        );
    }

    // Vérification d'identité (KYC) — une par client
    public function kycVerification()
    {
        return $this->hasOne(KycVerification::class);
    }

    // Coordonnées bancaires attribuées par un admin
    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }

    // Carte attribuée par un admin
    public function card()
    {
        return $this->hasOne(Card::class);
    }
}
