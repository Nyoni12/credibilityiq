<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->index(['company_id', 'status']);
            $table->index(['status', 'closed_at']);
            $table->index(['status', 'overall_score']);
        });

        Schema::table('survey_responses', function (Blueprint $table) {
            $table->index('assessment_id');
        });

        Schema::table('value_ratings', function (Blueprint $table) {
            $table->index(['company_value_id', 'training_recommended']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('industry');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['company_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::table('assessments',     fn ($t) => $t->dropIndex(['company_id','status']));
        Schema::table('assessments',     fn ($t) => $t->dropIndex(['status','closed_at']));
        Schema::table('assessments',     fn ($t) => $t->dropIndex(['status','overall_score']));
        Schema::table('survey_responses',fn ($t) => $t->dropIndex(['assessment_id']));
        Schema::table('value_ratings',   fn ($t) => $t->dropIndex(['company_value_id','training_recommended']));
        Schema::table('companies',       fn ($t) => $t->dropIndex(['is_active']));
        Schema::table('companies',       fn ($t) => $t->dropIndex(['industry']));
        Schema::table('users',           fn ($t) => $t->dropIndex(['company_id','role']));
    }
};
