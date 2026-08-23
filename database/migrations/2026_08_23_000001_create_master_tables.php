<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Nepal administrative & reference master tables ──────────────────
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('state_name_np')->unique();
            $table->string('state_name_en')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('district_name_np')->nullable();
            $table->string('district_name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('local_bodies', function (Blueprint $table) {
            $table->id();
            $table->string('local_body_name_np')->nullable();
            $table->string('local_body_name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Geo reference (province → district → local body) ────────────────
        Schema::table('districts', function (Blueprint $table) {
            $table->unsignedBigInteger('state_id')->nullable()->after('id');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('restrict');
        });
        Schema::table('local_bodies', function (Blueprint $table) {
            $table->unsignedBigInteger('district_id')->nullable()->after('id');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('restrict');
        });

        // ── Caste / Religion / Mother tongue special-category masters ───────
        Schema::create('castes', function (Blueprint $table) {
            $table->id();
            $table->string('caste_name_np')->nullable();
            $table->string('caste_name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('religions', function (Blueprint $table) {
            $table->id();
            $table->string('religion_name_np')->nullable();
            $table->string('religion_name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('mother_tongues', function (Blueprint $table) {
            $table->id();
            $table->string('mother_tongue_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('physically_challenged_classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_name_np')->nullable();
            $table->string('class_name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Samuha / Barga (candidate category/reservation) ────────────────
        Schema::create('samuha_bargas', function (Blueprint $table) {
            $table->id();
            $table->string('samuha_barga_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Boards / Universities / Faculties / Qualifications ──────────────
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->string('board_name_en')->nullable()->unique();
            $table->string('board_name_np')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('faculty_name_np')->nullable();
            $table->string('faculty_name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->string('qualification_name_np')->nullable();
            $table->string('qualification_name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Post hierarchy: service → group → sub_group → post ──────────────
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name_np')->nullable();
            $table->string('service_name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name_np')->nullable();
            $table->string('group_name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sub_groups', function (Blueprint $table) {
            $table->id();
            $table->string('sub_group_name_np')->nullable();
            $table->string('sub_group_name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('post_name');
            $table->string('remarks')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('post_combinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->restrictOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->foreignId('group_id')->constrained('groups')->restrictOnDelete();
            $table->foreignId('sub_group_id')->nullable()->constrained('sub_groups')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['post_id', 'service_id', 'group_id', 'sub_group_id'], 'uk_post_combination');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_combinations');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('sub_groups');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('services');
        Schema::dropIfExists('qualifications');
        Schema::dropIfExists('faculties');
        Schema::dropIfExists('boards');
        Schema::dropIfExists('samuha_bargas');
        Schema::dropIfExists('physically_challenged_classes');
        Schema::dropIfExists('mother_tongues');
        Schema::dropIfExists('religions');
        Schema::dropIfExists('castes');
        Schema::dropIfExists('local_bodies');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('states');
    }
};