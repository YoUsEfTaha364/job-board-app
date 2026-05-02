<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->enum("status",["pending","accepted","rejected"])->default("pending");
            $table->float("ai_generated_score")->default(0);
            $table->text("ai_generated_feedback");

            $table->foreignUuid("job_vacancy_id")->references("id")->on("job_vacancies")->cascadeOnDelete();
            $table->foreignUuid("resume_id")->references("id")->on("resumes")->cascadeOnDelete();
            $table->foreignUuid("user_id")->references("id")->on("users")->cascadeOnDelete();
            $table->timestamps();
                $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
