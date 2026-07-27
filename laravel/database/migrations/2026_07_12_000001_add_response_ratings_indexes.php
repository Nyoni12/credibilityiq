<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('response_ratings', function (Blueprint $table) {
            $table->index('survey_response_id');
            $table->index('company_value_id');
        });
    }

    public function down(): void
    {
        Schema::table('response_ratings', function (Blueprint $table) {
            $table->dropIndex(['survey_response_id']);
            $table->dropIndex(['company_value_id']);
        });
    }
};
