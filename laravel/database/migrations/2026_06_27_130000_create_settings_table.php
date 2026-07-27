<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        $defaults = [
            ['key' => 'default_assessment_slots', 'value' => '1'],
            ['key' => 'support_email',             'value' => 'support@credibilityfactory.com'],
            ['key' => 'allow_self_registration',   'value' => '1'],
            ['key' => 'platform_announcement',     'value' => ''],
        ];

        foreach ($defaults as $row) {
            DB::table('settings')->insert(array_merge($row, [
                'created_at' => now(), 'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
