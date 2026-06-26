<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('title')->default('Credibility Assessment');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->decimal('total_leakage', 18, 2)->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->string('respondent_role')->nullable();
            $table->timestamps();
        });

        Schema::create('response_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_value_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score'); // 1–10
            $table->timestamps();
        });

        Schema::create('value_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_value_id')->constrained()->cascadeOnDelete();
            $table->decimal('avg_score', 5, 2);
            $table->decimal('financial_impact', 18, 2)->default(0);
            $table->boolean('training_recommended')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('value_ratings');
        Schema::dropIfExists('response_ratings');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('assessments');
    }
};
