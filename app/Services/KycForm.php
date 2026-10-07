<?php

namespace App\Services;

use App\Models\KycAnswer;
use App\Models\KycField;
use App\Models\KycVerification;
use App\Models\SiteContact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Formulaire de vérification d'identité configurable : champs actifs, découpage en 1 ou 2 étapes,
 * validation, lecture et enregistrement des réponses (champs natifs → colonnes existantes, personnalisés → kyc_answers).
 */
class KycForm
{
    /** Champs natifs stockés sur l'utilisateur. */
    private const USER_COLUMNS = ['birth_date', 'country', 'address', 'id_type', 'id_number', 'date_delivre', 'tax_number', 'activity'];

    /** Champs natifs « fichier » → colonne de kyc_verifications. */
    private const FILE_COLUMNS = [
        'id_document_front' => 'id_document_front_path',
        'id_document_back'  => 'id_document_back_path',
        'selfie'            => 'selfie_path',
    ];

    /** @return Collection<int, KycField> champs actifs, dans l'ordre d'affichage */
    public static function fields(): Collection
    {
        return KycField::active()->get();
    }

    /**
     * Étapes à présenter au client : [1 => champs, 2 => champs]. Une seule étape si l'administration
     * a choisi « 1 étape », ou si tous les champs actifs sont rangés dans la même étape.
     *
     * @return array<int, Collection<int, KycField>>
     */
    public static function steps(): array
    {
        $fields = self::fields();
        if ($fields->isEmpty()) {
            return [1 => $fields];
        }

        if ((int) SiteContact::current()->kyc_steps === 1) {
            return [1 => $fields->values()];
        }

        $groups = $fields->groupBy('step')->sortKeys()->map->values()->values()->all();

        return array_combine(range(1, count($groups)), $groups);
    }

    // ── Lecture ──

    public static function filePath(User $user, KycField $field): ?string
    {
        if (isset(self::FILE_COLUMNS[$field->key])) {
            $kyc = $user->kycVerification;

            return $kyc?->{self::FILE_COLUMNS[$field->key]};
        }

        return KycAnswer::where('user_id', $user->id)->where('field_key', $field->key)->value('file_path');
    }

    /** Valeur déjà enregistrée (texte, date au format Y-m-d, ou chemin du fichier). */
    public static function value(User $user, KycField $field)
    {
        if ($field->isFile()) {
            return self::filePath($user, $field);
        }
        if ($field->builtin && in_array($field->key, self::USER_COLUMNS, true)) {
            $v = $user->{$field->key};

            return $v instanceof \DateTimeInterface ? $v->format('Y-m-d') : $v;
        }

        return KycAnswer::where('user_id', $user->id)->where('field_key', $field->key)->value('value');
    }

    public static function hasValue(User $user, KycField $field): bool
    {
        $v = self::value($user, $field);

        return $v !== null && trim((string) $v) !== '';
    }

    /** Vrai tant qu'un champ obligatoire de la liste n'est pas renseigné. */
    public static function missingRequired(User $user, Collection $fields): bool
    {
        return $fields->contains(fn (KycField $f) => $f->required && ! self::hasValue($user, $f));
    }

    // ── Validation ──

    public static function rules(Collection $fields, User $user): array
    {
        $rules = [];
        foreach ($fields as $f) {
            $presence = $f->required && ! self::hasValue($user, $f) ? 'required' : 'nullable';
            $rules[$f->key] = match (true) {
                $f->type === 'image'    => [$presence, 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
                $f->type === 'file'     => [$presence, 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
                $f->type === 'date'     => array_filter([$presence, 'date', $f->builtin ? ($f->key === 'birth_date' ? 'before:today' : 'before_or_equal:today') : null]),
                $f->type === 'doc_type' => [$presence, 'string', 'in:cni,passeport,permis'],
                $f->type === 'select'   => [$presence, 'string', 'in:' . implode(',', array_map(fn ($o) => str_replace(',', ' ', $o), $f->options ?? []))],
                $f->type === 'textarea' => [$presence, 'string', 'max:2000'],
                $f->key === 'address'   => [$presence, 'string', 'max:500'],
                $f->type === 'country'  => [$presence, 'string', 'max:100'],
                default                 => [$presence, 'string', 'max:255'],
            };
        }

        return $rules;
    }

    public static function attributes(Collection $fields): array
    {
        return $fields->mapWithKeys(fn (KycField $f) => [$f->key => $f->displayLabel()])->all();
    }

    // ── Enregistrement ──

    public static function save(User $user, Collection $fields, Request $request, KycDocumentService $documents): void
    {
        $userData = [];

        foreach ($fields as $f) {
            if ($f->isFile()) {
                if (! $request->hasFile($f->key)) {
                    continue;                                     // fichier déjà envoyé : conservé
                }
                $old  = self::filePath($user, $f);
                $path = $documents->store($request->file($f->key), $user->id, $f->key);
                if ($old) {
                    $documents->delete($old);
                }
                if (isset(self::FILE_COLUMNS[$f->key])) {
                    $user->kycVerification()->updateOrCreate(['user_id' => $user->id], [
                        self::FILE_COLUMNS[$f->key] => $path,
                    ] + ($user->kycVerification ? [] : ['status' => KycVerification::STATUS_NON_SOUMIS]));
                    $user->unsetRelation('kycVerification');
                } else {
                    KycAnswer::updateOrCreate(['user_id' => $user->id, 'field_key' => $f->key], ['file_path' => $path]);
                }
                continue;
            }

            $value = $request->input($f->key);
            $value = is_string($value) ? trim($value) : $value;
            $value = ($value === '' ? null : $value);

            if ($f->builtin && in_array($f->key, self::USER_COLUMNS, true)) {
                $userData[$f->key] = $value;
            } else {
                KycAnswer::updateOrCreate(['user_id' => $user->id, 'field_key' => $f->key], ['value' => $value]);
            }
        }

        if ($userData) {
            $user->update($userData);
        }
    }

    /** Étape (1..n) à afficher par défaut : la première qui a encore un champ obligatoire vide, sinon la dernière. */
    public static function firstIncompleteStep(User $user, array $steps): int
    {
        foreach ($steps as $n => $fields) {
            if (self::missingRequired($user, $fields)) {
                return $n;
            }
        }

        return array_key_last($steps);
    }
}
