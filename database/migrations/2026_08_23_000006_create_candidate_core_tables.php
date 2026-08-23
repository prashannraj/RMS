<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Candidate master data (1:1 with users)
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->unique('user_id');

            $table->string('first_name', 30);
            $table->string('first_name_nepali', 30)->nullable();
            $table->string('middle_name', 30)->nullable();
            $table->string('middle_name_nepali', 30)->nullable();
            $table->string('last_name', 30)->nullable();
            $table->string('last_name_nepali', 30)->nullable();

            $table->date('date_of_birth_ad');
            $table->string('date_of_birth_bs', 15);

            $table->string('citizenship_no', 30);
            $table->string('national_id', 30)->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->foreign('district_id')->references('id')->on('districts')->restrictOnDelete();
            $table->string('issued_date', 15)->nullable();
            $table->string('citizenship_issued_date_bs', 15)->nullable();

            $table->string('gender', 30);
            $table->string('candidate_photograph')->nullable();
            $table->string('candidate_signature')->nullable();
            $table->string('candidate_citizenship')->nullable();
            $table->string('candidate_citizenship_backside')->nullable();

            $table->string('husband_wife_first_name', 30)->nullable();
            $table->string('husband_wife_middle_name', 30)->nullable();
            $table->string('husband_wife_last_name', 30)->nullable();
            $table->string('father_first_name', 30)->nullable();
            $table->string('father_middle_name', 30)->nullable();
            $table->string('father_last_name', 30)->nullable();
            $table->string('grand_father_first_name', 30)->nullable();
            $table->string('grand_father_middle_name', 30)->nullable();
            $table->string('grand_father_last_name', 30)->nullable();
            $table->string('mother_first_name', 30)->nullable();
            $table->string('mother_middle_name', 30)->nullable();
            $table->string('mother_last_name', 30)->nullable();

            $table->string('created_date', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Candidate address (1:1 with candidate master)
        Schema::create('candidate_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id');
            $table->foreign('candidate_id')->references('id')->on('candidates')->cascadeOnDelete();
            $table->unique('candidate_id');

            $table->string('ward_no')->nullable();
            $table->string('tole_name')->nullable();
            $table->string('marga')->nullable();
            $table->string('house_no')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('mobile_no');
            $table->string('email')->nullable();
            $table->string('mailing_address')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->foreign('district_id')->references('id')->on('districts')->restrictOnDelete();
            $table->unsignedBigInteger('local_body_id')->nullable();
            $table->foreign('local_body_id')->references('id')->on('local_bodies')->restrictOnDelete();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->foreign('state_id')->references('id')->on('states')->restrictOnDelete();
            $table->timestamps();
        });

        // Candidate extra details (1:1)
        Schema::create('candidate_extra_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id');
            $table->foreign('candidate_id')->references('id')->on('candidates')->cascadeOnDelete();
            $table->unique('candidate_id');

            $table->string('religion_other')->nullable();
            $table->string('marital_status')->nullable();
            $table->boolean('physically_challenged')->default(false);
            $table->string('physically_challenged_description')->nullable();
            $table->string('father_education')->nullable();
            $table->string('father_education_level')->nullable();
            $table->string('mother_education')->nullable();
            $table->string('mother_education_level')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_occupation_other')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_occupation_other')->nullable();
            $table->string('area')->nullable();
            $table->unsignedBigInteger('caste_id')->nullable();
            $table->foreign('caste_id')->references('id')->on('castes')->restrictOnDelete();
            $table->string('caste_other')->nullable();
            $table->unsignedBigInteger('mother_tongue_id')->nullable();
            $table->foreign('mother_tongue_id')->references('id')->on('mother_tongues')->restrictOnDelete();
            $table->string('mother_tongue_other')->nullable();
            $table->unsignedBigInteger('physically_challenged_class_id')->nullable();
            $table->foreign('physically_challenged_class_id')->references('id')->on('physically_challenged_classes')->restrictOnDelete();
            $table->unsignedBigInteger('religion_id')->nullable();
            $table->foreign('religion_id')->references('id')->on('religions')->restrictOnDelete();
            $table->string('employment_status')->nullable();
            $table->string('employment_status_other')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_extra_details');
        Schema::dropIfExists('candidate_addresses');
        Schema::dropIfExists('candidates');
    }
};