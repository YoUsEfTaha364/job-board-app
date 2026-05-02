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
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("title");
            $table->text("description");
            $table->string("location");
            $table->decimal("salary");
            $table->enum("type",["full-time","hybrid","remote","contract"])->default("full-time");

            $table->foreignUuid("company_id")->references("id")->on("companies")->cascadeOnDelete();
            $table->foreignUuid("category_id")->references("id")->on("job_categories")->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};
