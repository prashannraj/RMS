<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Exam center (master data)
        Schema::create('exam_centers', function (Blueprint $table) {
            $table->id();
            $table->string('exam_center_name_np')->unique();
            $table->string('exam_center_name_en')->nullable();
            $table->unsignedBigInteger('state_id');
            $table->foreign('state_id')->references('id')->on('states')->restrictOnDelete();
            $table->unsignedBigInteger('district_id');
            $table->foreign('district_id')->references('id')->on('districts')->restrictOnDelete();
            $table->string('address');
            $table->string('contact_person_name_np');
            $table->string('contact_person_name_en')->nullable();
            $table->string('contact_number');
            $table->string('contact_email');
            $table->integer('center_capacity');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Exam center allocated to advertisement code
        Schema::create('exam_center_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertisement_code_id');
            $table->foreign('advertisement_code_id')->references('id')->on('advertisement_codes')->restrictOnDelete();
            $table->unsignedBigInteger('exam_center_id');
            $table->foreign('exam_center_id')->references('id')->on('exam_centers')->restrictOnDelete();
            $table->integer('allocated_count');
            $table->boolean('room_allocation_finalised')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            // JPA unique: (advertisement_code_id, exam_center_id)
            $table->unique(['advertisement_code_id', 'exam_center_id'], 'unique_advtcode_examcenter');
        });

        // Room allocated to exam center
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_center_allocation_id');
            $table->foreign('exam_center_allocation_id')->references('id')->on('exam_center_allocations')->restrictOnDelete();
            $table->string('room_no');
            $table->integer('allocated_count');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            // JPA unique: (exam_center_allocated_to_advt_code_id, room_no)
            $table->unique(['exam_center_allocation_id', 'room_no'], 'uk_examcenterallocation_room');
        });

        // Invigilator post master
        Schema::create('invigilator_posts', function (Blueprint $table) {
            $table->id();
            $table->string('post_name_np')->unique();
            $table->string('post_name_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Invigilator master
        Schema::create('invigilators', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no')->nullable()->unique();
            $table->string('first_name_np');
            $table->string('middle_name_np')->nullable();
            $table->string('last_name_np');
            $table->string('first_name_en')->nullable();
            $table->string('middle_name_en')->nullable();
            $table->string('last_name_en')->nullable();
            $table->string('mobile_no');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            // JPA unique: (first_name_np, middle_name_np, last_name_np)
            $table->unique(['first_name_np', 'middle_name_np', 'last_name_np'], 'uc_invigilator_name');
        });

        // Invigilator allocated to exam center
        Schema::create('invigilator_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_center_allocation_id');
            $table->foreign('exam_center_allocation_id')->references('id')->on('exam_center_allocations')->restrictOnDelete();
            $table->unsignedBigInteger('invigilator_post_id');
            $table->foreign('invigilator_post_id')->references('id')->on('invigilator_posts')->restrictOnDelete();
            $table->unsignedBigInteger('invigilator_id');
            $table->foreign('invigilator_id')->references('id')->on('invigilators')->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            // JPA unique: (exam_center_allocation_id, invigilator_id)
            $table->unique(['exam_center_allocation_id', 'invigilator_id'], 'uc_examcenter_invigilator');
        });

        // Paper for curriculum (paper master for exams)
        Schema::create('papers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_id')->nullable();
            $table->foreign('curriculum_id')->references('id')->on('master_data_curriculums')->restrictOnDelete();
            $table->string('paper_code')->nullable();
            $table->string('paper_name')->nullable();
            $table->string('paper_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('fullmarks')->nullable();
            $table->string('minimum_passing_percentage')->nullable();
            $table->string('duration')->nullable();
            $table->string('exam_option')->nullable();
            $table->string('test_type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['curriculum_id', 'paper_code']);
        });

        // Exam scheduling (exam date/time per paper + requisition)
        Schema::create('exam_schedulings', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('starttime')->nullable();
            $table->string('endtime')->nullable();
            $table->date('exam_date')->nullable();
            $table->unsignedBigInteger('paper_id')->nullable();
            $table->foreign('paper_id')->references('id')->on('papers')->restrictOnDelete();
            $table->string('status')->default('scheduled');
            $table->unsignedBigInteger('requisition_id')->nullable();
            $table->foreign('requisition_id')->references('id')->on('requisitions')->restrictOnDelete();
            $table->timestamps();
        });

        // Candidate appear against advertisement code (exam allocation)
        Schema::create('candidate_exams', function (Blueprint $table) {
            $table->id();
            $table->string('advertisement_code');
            $table->unsignedBigInteger('candidate_id');
            $table->foreign('candidate_id')->references('id')->on('candidates')->restrictOnDelete();
            $table->string('application_number', 50)->nullable();
            $table->integer('age_on_last_date_day')->nullable();
            $table->integer('age_on_last_date_month')->nullable();
            $table->integer('age_on_last_date_year')->nullable();
            $table->string('examination_center_id')->nullable();
            $table->integer('roll_no')->nullable();
            $table->unsignedBigInteger('room_allocated_id')->nullable();
            $table->foreign('room_allocated_id')->references('id')->on('rooms')->nullOnDelete();
            $table->string('attendance_status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('result_status')->nullable();
            $table->string('exam_results_status')->nullable();
            $table->string('interview_date')->nullable();
            $table->string('interview_marks_allocation_status')->nullable();
            $table->string('interview_scheduled_status')->nullable();
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->decimal('total_marks', 8, 2)->nullable();
            $table->string('scrutiny_status')->default('pending');
            $table->string('remarks')->nullable();
            $table->timestamps();
            // JPA one-to-one with challan; index/candidate uniqueness added for query speed
            $table->index(['candidate_id', 'advertisement_code']);
            $table->index(['roll_no', 'advertisement_code']);
        });

        // Duplicate admit card
        Schema::create('admit_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_exam_id');
            $table->foreign('candidate_exam_id')->references('id')->on('candidate_exams')->cascadeOnDelete();
            $table->unsignedBigInteger('candidate_id')->nullable();
            $table->foreign('candidate_id')->references('id')->on('candidates')->restrictOnDelete();
            $table->string('advt_code')->nullable();
            $table->string('admit_card_number')->nullable()->unique();
            $table->string('roll_number')->nullable();
            $table->dateTime('issued_at')->nullable();
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->foreign('issued_by')->references('id')->on('users')->nullOnDelete();
            $table->string('status')->default('issued');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admit_cards');
        Schema::dropIfExists('candidate_exams');
        Schema::dropIfExists('exam_schedulings');
        Schema::dropIfExists('papers');
        Schema::dropIfExists('invigilator_allocations');
        Schema::dropIfExists('invigilators');
        Schema::dropIfExists('invigilator_posts');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('exam_center_allocations');
        Schema::dropIfExists('exam_centers');
    }
};