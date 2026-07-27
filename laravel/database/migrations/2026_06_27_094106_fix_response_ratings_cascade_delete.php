<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('response_ratings', function (Blueprint $table) {
            // Drop the CASCADE — company_values deletion was silently wiping all survey data
            $table->dropForeign(['company_value_id']);
            // RESTRICT: database will error instead of silently deleting survey data
            $table->foreign('company_value_id')
                  ->references('id')->on('company_values')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('response_ratings', function (Blueprint $table) {
            $table->dropForeign(['company_value_id']);
            $table->foreign('company_value_id')
                  ->references('id')->on('company_values')
                  ->onDelete('cascade');
        });
    }
};
