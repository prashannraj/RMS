<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Advertisement code (official advertisement number)
        Schema::create('advertisement_codes', function (Blueprint $table) {
            $table->id();
            $table->string('advertisement_code')->unique();
            $table->string('advertisement_published_date_en')->nullable();
            $table->string('advertisement_published_date_np')->nullable();
            $table->string('block_range_firstcode')->nullable();
            $table->string('block_range_secondcode')->nullable();
            $table->foreignId('master_curri_id')->nullable()->constrained('master_data_curriculums')->nullOnDelete();
            $table->date('last_date_for_submission')->nullable();
            $table->string('last_date_for_submission_np')->nullable();
            $table->string('lifecycle_status')->default('draft');
            $table->string('memorandum_number')->nullable();
            $table->date('payment_last_date_en')->nullable();
            $table->string('payment_last_date_np')->nullable();
            $table->string('pending_at')->nullable();
            $table->string('postexam_status')->nullable();
            $table->string('question_selection_status')->nullable();
            $table->unsignedBigInteger('requesting_office_id')->nullable();
            $table->foreign('requesting_office_id')->references('id')->on('organizations')->nullOnDelete();
            $table->string('status_of_lastdate_of_submission')->default('pending');
            $table->string('exam_scheduling_status')->default('pending');
            $table->string('remarks')->nullable();
            $table->date('cut_off_date')->nullable();
            $table->string('cut_off_date_np')->nullable();
            $table->string('scrutiny_status')->default('pending');
            $table->string('double_fee_last_date_en')->nullable();
            $table->string('double_fee_last_date_np')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Advertisement (published notice referencing requisition + quota)
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('advertisementcode')->nullable();
            $table->string('advertisementnumber');
            $table->foreignId('quota_id')->constrained('quotas')->restrictOnDelete();
            $table->string('status')->default('draft');
            $table->integer('vacancy')->default(0);
            $table->foreignId('requisition_id')->constrained('requisitions')->restrictOnDelete();
            $table->date('published_date_en')->nullable();
            $table->string('published_date_np')->nullable();
            $table->timestamp('application_start_at')->nullable();
            $table->timestamp('application_end_at')->nullable();
            $table->timestamp('double_fee_deadline_at')->nullable();
            $table->text('description')->nullable();
            $table->text('description_np')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['status', 'requisition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('advertisement_codes');
    }
};