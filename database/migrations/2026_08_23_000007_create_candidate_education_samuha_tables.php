<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Candidate education ──────────────────────────────────────────
        Schema::create('candidate_education_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id');
            $table->foreign('candidate_id')->references('id')->on('candidates')->cascadeOnDelete();

            $table->string('university_or_board_name', 200);
            $table->string('level', 30);
            $table->string('faculty', 50)->nullable();
            $table->decimal('percentage', 5, 2);
            $table->string('major_subject', 50)->nullable();
            $table->string('description', 200)->nullable();
            $table->string('education_type', 15);
            $table->date('passed_date_ad');
            $table->string('passed_date_bs', 15)->nullable();
            $table->string('transcript', 150)->nullable();
            $table->string('character_certificate', 150)->nullable();
            $table->string('equivalent_certificate', 150)->nullable();
            $table->string('division')->nullable();
            $table->timestamps();
            $table->index(['candidate_id', 'level']);
        });

        // ── Candidate SamuhaBarga (candidate category/reservation group) ──
        Schema::create('candidate_samuha_bargas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id');
            $table->foreign('candidate_id')->references('id')->on('candidates')->cascadeOnDelete();
            $table->unsignedBigInteger('samuha_barga_id');
            $table->foreign('samuha_barga_id')->references('id')->on('samuha_bargas')->restrictOnDelete();
            $table->string('candidate_samuha_barga_doc')->nullable();
            $table->string('candidate_samuha_barga_other')->nullable();
            $table->unique(['candidate_id', 'samuha_barga_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_samuha_bargas');
        Schema::dropIfExists('candidate_education_details');
    }
};