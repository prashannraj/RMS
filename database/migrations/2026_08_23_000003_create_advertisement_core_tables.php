<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Quota (advertisement category: open/internal)
        Schema::create('quotas', function (Blueprint $table) {
            $table->id();
            $table->string('quota_name');
            $table->string('remarks')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Master data curriculum (qualification/curriculum master)
        Schema::create('master_data_curriculums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->nullable()->constrained('posts')->nullOnDelete();
            $table->foreignId('master_division_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->foreignId('post_id')->nullable()->constrained('posts')->nullOnDelete();
            $table->string('post_type')->nullable();
            $table->string('category')->nullable(); // OPEN / INTERNAL / OPEN_INTERNAL
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Requisition (demand by requesting office for posts/vacancies)
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('fiscal_year')->nullable();
            $table->unsignedBigInteger('requesting_office_id')->nullable();
            $table->foreign('requesting_office_id')->references('id')->on('organizations')->nullOnDelete();
            $table->unsignedBigInteger('master_id')->nullable();
            $table->foreign('master_id')->references('id')->on('master_data_curriculums')->nullOnDelete();
            $table->string('demand_office');
            $table->string('requisition_remarks')->nullable();
            $table->string('remarks_for_cancellation')->nullable();
            $table->string('work_summary')->nullable();
            $table->timestamp('requested_date')->nullable();
            $table->integer('total_vacancy')->default(0);
            $table->string('letter_url')->nullable();
            $table->string('status')->default('draft');
            $table->string('distribution_flag')->default('pending');
            $table->string('exam_scheduling_flag')->default('pending');
            $table->string('exam_scheduling_remarks')->nullable();
            $table->boolean('exam_skippable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['status', 'fiscal_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisitions');
        Schema::dropIfExists('master_data_curriculums');
        Schema::dropIfExists('quotas');
    }
};