<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycAnswer;
use App\Models\KycField;
use App\Models\Language;
use App\Models\SiteContact;
use App\Services\KycDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Configuration du formulaire de vérification d'identité : champs, ordre, étapes (1 ou 2). */
class KycFieldController extends Controller
{
    public function index()
    {
        $fields = KycField::orderBy('step')->orderBy('sort')->orderBy('id')->get();
        $steps  = (int) SiteContact::current()->kyc_steps ?: 2;
        $languages = Language::enabledList();

        return view('admin.kyc.fields', compact('fields', 'steps', 'languages'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'kyc_steps'          => 'required|in:1,2',
            'fields'             => 'required|array',
            'fields.*.step'      => 'required|in:1,2',
            'fields.*.sort'      => 'nullable|integer|min:0|max:9999',
            'fields.*.label'     => 'nullable|string|max:150',
            'fields.*.options'   => 'nullable|string|max:2000',
            'fields.*.labels'    => 'nullable|array',
            'fields.*.labels.*'  => 'nullable|string|max:150',
            'fields.*.options_i18n'   => 'nullable|array',
            'fields.*.options_i18n.*' => 'nullable|string|max:2000',
        ]);
        $locales = Language::enabledCodes();

        $fields = KycField::all()->keyBy('id');

        $enabledCount = 0;
        foreach ($data['fields'] as $id => $row) {
            $f = $fields->get((int) $id);
            if (! $f) {
                continue;
            }
            $enabled = ! empty($request->input("fields.$id.enabled"));
            $enabledCount += $enabled ? 1 : 0;

            $update = [
                'enabled'  => $enabled,
                'required' => ! empty($request->input("fields.$id.required")),
                'step'     => (int) $row['step'],
                'sort'     => (int) ($row['sort'] ?? $f->sort),
            ];

            if (! $f->builtin) {
                $label = trim((string) ($row['label'] ?? ''));
                if ($label === '') {
                    return back()->withErrors(['fields' => 'Le libellé d’un champ personnalisé est obligatoire.'])->withInput();
                }
                $update['label']  = $label;
                $update['labels'] = self::cleanLabels($row['labels'] ?? [], $locales);
                if ($f->type === 'select') {
                    $options = self::parseOptions($row['options'] ?? '');
                    if (count($options) < 2) {
                        return back()->withErrors(['fields' => 'La liste « ' . $label . ' » doit proposer au moins 2 choix (un par ligne).'])->withInput();
                    }
                    $update['options'] = $options;
                    $i18n = self::cleanOptionTranslations($row['options_i18n'] ?? [], $locales, count($options));
                    if ($i18n === false) {
                        return back()->withErrors(['fields' => 'Les traductions des choix de « ' . $label . ' » doivent avoir autant de lignes que la liste par défaut (' . count($options) . ').'])->withInput();
                    }
                    $update['options_i18n'] = $i18n;
                }
            }

            $f->update($update);
        }

        if ($enabledCount === 0) {
            return back()->withErrors(['fields' => 'Au moins un champ doit rester actif.'])->withInput();
        }

        SiteContact::current()->update(['kyc_steps' => (int) $data['kyc_steps']]);

        return redirect()->route('admin.kyc.fields')->with('success', 'Formulaire de vérification d’identité enregistré.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'   => 'required|string|max:150',
            'type'    => 'required|in:' . implode(',', array_keys(KycField::CUSTOM_TYPES)),
            'step'    => 'required|in:1,2',
            'options' => 'nullable|string|max:2000',
            'labels'  => 'nullable|array',
            'labels.*' => 'nullable|string|max:150',
            'options_i18n'   => 'nullable|array',
            'options_i18n.*' => 'nullable|string|max:2000',
        ]);
        $locales = Language::enabledCodes();

        $options = null;
        $i18n    = null;
        if ($data['type'] === 'select') {
            $options = self::parseOptions($data['options'] ?? '');
            if (count($options) < 2) {
                return back()->withErrors(['label' => 'Une liste de choix doit proposer au moins 2 options (une par ligne).'])->withInput();
            }
            $i18n = self::cleanOptionTranslations($data['options_i18n'] ?? [], $locales, count($options));
            if ($i18n === false) {
                return back()->withErrors(['label' => 'Les traductions des choix doivent avoir autant de lignes que la liste par défaut (' . count($options) . ').'])->withInput();
            }
        }

        KycField::create([
            'key'      => 'c_' . Str::limit(Str::slug($data['label'], '_'), 30, '') . '_' . Str::lower(Str::random(4)),
            'label'    => trim($data['label']),
            'labels'   => self::cleanLabels($data['labels'] ?? [], $locales),
            'type'     => $data['type'],
            'options'  => $options,
            'options_i18n' => $i18n,
            'step'     => (int) $data['step'],
            'required' => $request->boolean('required'),
            'enabled'  => true,
            'builtin'  => false,
            'sort'     => ((int) KycField::max('sort')) + 10,
        ]);

        return redirect()->route('admin.kyc.fields')->with('success', 'Champ « ' . trim($data['label']) . ' » ajouté.');
    }

    /** Supprime un champ personnalisé et les réponses (fichiers compris) déjà enregistrées. */
    public function destroy(KycField $field, KycDocumentService $documents)
    {
        abort_if($field->builtin, 403, 'Un champ natif ne peut pas être supprimé : désactivez-le.');

        KycAnswer::where('field_key', $field->key)->get()->each(function (KycAnswer $a) use ($documents) {
            $documents->delete($a->file_path);
            $a->delete();
        });
        $field->delete();

        return redirect()->route('admin.kyc.fields')->with('success', 'Champ supprimé.');
    }

    /** Traductions du libellé : uniquement les langues disponibles, sans valeur vide. */
    private static function cleanLabels(array $labels, array $locales): ?array
    {
        $out = [];
        foreach ($labels as $code => $text) {
            $text = trim((string) $text);
            if ($text !== '' && in_array($code, $locales, true)) {
                $out[$code] = $text;
            }
        }

        return $out ?: null;
    }

    /** Traductions des choix : une ligne par choix de la liste par défaut ; false si le nombre de lignes ne correspond pas. */
    private static function cleanOptionTranslations(array $raw, array $locales, int $count): array|false|null
    {
        $out = [];
        foreach ($raw as $code => $text) {
            if (! in_array($code, $locales, true) || trim((string) $text) === '') {
                continue;
            }
            $lines = array_map(fn ($l) => trim(str_replace(',', ' ', $l)), preg_split('/\R/', trim((string) $text)));
            if (count($lines) !== $count) {
                return false;
            }
            $out[$code] = $lines;
        }

        return $out ?: null;
    }

    /** Une option par ligne, sans doublon ni ligne vide. */
    private static function parseOptions(string $raw): array
    {
        return array_values(array_unique(array_filter(array_map(fn ($l) => trim(str_replace(',', ' ', $l)), preg_split('/\R/', $raw)))));
    }
}
