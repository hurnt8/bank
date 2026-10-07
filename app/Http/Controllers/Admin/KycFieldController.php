<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycAnswer;
use App\Models\KycField;
use App\Models\Language;
use App\Models\SiteContact;
use App\Services\KycDocumentService;
use App\Services\KycTranslator;
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
        $toTranslate  = [];
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
                $labelsIn = self::cleanLabels($row['labels'] ?? [], $locales) ?? [];
                $optsIn   = null;
                $options  = null;
                if ($f->type === 'select') {
                    $options = self::parseOptions($row['options'] ?? '');
                    if (count($options) < 2) {
                        return back()->withErrors(['fields' => 'La liste « ' . $label . ' » doit proposer au moins 2 choix (un par ligne).'])->withInput();
                    }
                    $update['options'] = $options;
                    $optsIn = self::cleanOptionTranslations($row['options_i18n'] ?? [], $locales, count($options));
                    if ($optsIn === false) {
                        return back()->withErrors(['fields' => 'Les traductions des choix de « ' . $label . ' » doivent avoir autant de lignes que la liste par défaut (' . count($options) . ').'])->withInput();
                    }
                }
                $changed = $label !== $f->label || ($f->type === 'select' && $options !== array_values($f->options ?? []));
                [$update['labels'], $update['options_i18n'], $update['auto_locales']] = self::retainAuto(
                    $labelsIn, $optsIn ?? [], $locales, $f->auto_locales ?? [], $f->labels ?? [], $f->options_i18n ?? [], $changed
                );
                $toTranslate[] = $f->id;
            }

            $f->update($update);
        }

        if ($enabledCount === 0) {
            return back()->withErrors(['fields' => 'Au moins un champ doit rester actif.'])->withInput();
        }

        SiteContact::current()->update(['kyc_steps' => (int) $data['kyc_steps']]);

        foreach ($toTranslate as $id) {
            self::translateLater($id);
        }

        return redirect()->route('admin.kyc.fields')->with('success', 'Formulaire de vérification d’identité enregistré.' . ($toTranslate ? self::autoNote() : ''));
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

        $field = KycField::create([
            'key'      => 'c_' . Str::limit(Str::slug($data['label'], '_'), 30, '') . '_' . Str::lower(Str::random(4)),
            'label'    => trim($data['label']),
            'labels'   => self::cleanLabels($data['labels'] ?? [], $locales),
            'type'     => $data['type'],
            'options'  => $options,
            'options_i18n' => is_array($i18n) ? $i18n : null,
            'step'     => (int) $data['step'],
            'required' => $request->boolean('required'),
            'enabled'  => true,
            'builtin'  => false,
            'sort'     => ((int) KycField::max('sort')) + 10,
        ]);

        self::translateLater($field->id);

        return redirect()->route('admin.kyc.fields')->with('success', 'Champ « ' . trim($data['label']) . ' » ajouté.' . self::autoNote());
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

    private static function autoNote(): string
    {
        return ' Traduction automatique dans toutes les langues en cours : rechargez la page dans quelques secondes pour la voir (vous pourrez ensuite la corriger).';
    }

    /**
     * Garde les traductions automatiques toujours valables et retire celles dont le libellé ou les choix de départ ont changé
     * (elles seront régénérées). Une traduction modifiée à la main n'est plus considérée comme automatique.
     *
     * @return array{0: ?array, 1: ?array, 2: ?array} [libellés, choix traduits, langues automatiques]
     */
    private static function retainAuto(array $labels, array $optionsI18n, array $locales, array $autoPrev, array $labelsPrev, array $optionsPrev, bool $changed): array
    {
        $auto = [];
        foreach ($locales as $code) {
            if (! in_array($code, $autoPrev, true)) {
                continue;
            }
            $same = ($labels[$code] ?? null) === ($labelsPrev[$code] ?? null) && ($optionsI18n[$code] ?? null) === ($optionsPrev[$code] ?? null);
            if (! $same) {
                continue;                           // corrigée à la main
            }
            if ($changed) {
                unset($labels[$code], $optionsI18n[$code]);   // texte de départ modifié : à régénérer
            } else {
                $auto[] = $code;
            }
        }

        return [$labels ?: null, $optionsI18n ?: null, $auto ?: null];
    }

    /** Lance la traduction automatique dans un processus indépendant : elle peut durer plusieurs dizaines de secondes. */
    private static function translateLater(int $id): void
    {
        // Sous Apache, PHP_BINARY désigne httpd : on retombe alors sur l'exécutable php du dossier PHP
        $php = str_contains(strtolower(basename(PHP_BINARY)), 'php') ? PHP_BINARY : rtrim(PHP_BINDIR, '\/') . DIRECTORY_SEPARATOR . (PHP_OS_FAMILY === 'Windows' ? 'php.exe' : 'php');
        if (! is_file($php)) {
            $php = 'php';
        }
        $artisan = base_path('artisan');

        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'start /B "" ' . escapeshellarg($php) . ' ' . escapeshellarg($artisan) . ' kyc:translate-field ' . (int) $id . ' > NUL 2>&1';
            pclose(popen($cmd, 'r'));
        } else {
            exec(escapeshellarg($php) . ' ' . escapeshellarg($artisan) . ' kyc:translate-field ' . (int) $id . ' > /dev/null 2>&1 &');
        }
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
