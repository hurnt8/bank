<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycField extends Model
{
    protected $fillable = ['key', 'label_key', 'label', 'type', 'options', 'step', 'required', 'enabled', 'builtin', 'sort'];

    protected $casts = [
        'options'  => 'array',
        'required' => 'boolean',
        'enabled'  => 'boolean',
        'builtin'  => 'boolean',
        'step'     => 'integer',
        'sort'     => 'integer',
    ];

    /** Types proposés pour un champ personnalisé. */
    public const CUSTOM_TYPES = [
        'text'     => 'Texte court',
        'textarea' => 'Texte long',
        'date'     => 'Date',
        'select'   => 'Liste de choix',
        'file'     => 'Fichier (image ou PDF)',
        'image'    => 'Photo (image uniquement)',
    ];

    /** Champs qui stockent un fichier. */
    public function isFile(): bool
    {
        return in_array($this->type, ['file', 'image'], true);
    }

    /** Libellé dans la langue courante : traduction pour un champ natif, texte saisi pour un champ personnalisé. */
    public function displayLabel(): string
    {
        return $this->builtin && $this->label_key ? __($this->label_key) : (string) ($this->label ?: $this->key);
    }

    public function scopeActive($query)
    {
        return $query->where('enabled', true)->orderBy('step')->orderBy('sort')->orderBy('id');
    }
}
