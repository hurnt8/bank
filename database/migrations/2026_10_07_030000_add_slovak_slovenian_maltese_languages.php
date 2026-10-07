<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Ajoute le slovaque, le slovène et le maltais au sélecteur de langue (idempotent). */
    public function up(): void
    {
        $languages = [
            ['code' => 'sk', 'native_name' => 'Slovenčina', 'flag_ext' => 'svg', 'sort_order' => 15],
            ['code' => 'sl', 'native_name' => 'Slovenščina', 'flag_ext' => 'png', 'sort_order' => 16],
            ['code' => 'mt', 'native_name' => 'Malti',       'flag_ext' => 'png', 'sort_order' => 17],
        ];

        foreach ($languages as $lang) {
            DB::table('languages')->insertOrIgnore($lang + [
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('languages')->whereIn('code', ['sk', 'sl', 'mt'])->delete();
    }
};
