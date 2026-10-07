<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycField extends Model
{
    protected $fillable = ['key', 'label_key', 'label', 'labels', 'type', 'options', 'options_i18n', 'auto_locales', 'step', 'required', 'enabled', 'builtin', 'sort'];

    protected $casts = [
        'options'  => 'array',
        'labels'   => 'array',
        'options_i18n' => 'array',
        'auto_locales' => 'array',
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

    /**
     * Libellé dans la langue du client : traduction automatique pour un champ natif ; pour un champ personnalisé,
     * la traduction saisie par l'administrateur pour cette langue, sinon le libellé par défaut.
     */
    public function displayLabel(?string $locale = null): string
    {
        if ($this->builtin && $this->label_key) {
            return __($this->label_key, [], $locale);
        }

        $locale = $locale ?: app()->getLocale();
        $tr     = trim((string) (($this->labels ?? [])[$locale] ?? ''));

        return $tr !== '' ? $tr : (string) ($this->label ?: $this->key);
    }

    /** Choix d'une liste : [valeur enregistrée => texte affiché dans la langue courante]. */
    public function displayOptions(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $values = array_values($this->options ?? []);
        $tr     = array_values(($this->options_i18n ?? [])[$locale] ?? []);
        $out    = [];
        foreach ($values as $i => $v) {
            $out[$v] = trim((string) ($tr[$i] ?? '')) !== '' ? trim($tr[$i]) : $v;
        }

        return $out;
    }

    public function scopeActive($query)
    {
        return $query->where('enabled', true)->orderBy('step')->orderBy('sort')->orderBy('id');
    }
}
