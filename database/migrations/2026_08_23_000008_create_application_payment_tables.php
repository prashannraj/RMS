<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Application (candidate appears to advertisement number)
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('advertisement_code');
            $table->string('advertisement_number', 50)->nullable();
            $table->unsignedBigInteger('candidate_id');
            $table->foreign('candidate_id')->references('id')->on('candidates')->restrictOnDelete();
            $table->decimal('deposited_fee', 14, 2)->default(0);
            $table->decimal('total_fee', 14, 2)->default(0);
            $table->string('payment_status')->default('unpaid');
            $table->string('result_status')->nullable();
            $table->text('remarks')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['candidate_id', 'advertisement_code']);
            $table->index(['advertisement_code', 'payment_status']);
        });

        // Challan (payment)
        Schema::create('challans', function (Blueprint $table) {
            $table->id();
            $table->string('advt_code');
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('challan_date')->nullable();
            $table->string('challan_time')->nullable();
            $table->string('name')->nullable();
            $table->string('office')->nullable();
            $table->string('status')->default('pending');
            $table->string('username')->nullable();
            $table->string('voucher_no')->nullable()->unique();
            $table->unsignedBigInteger('application_id')->nullable();
            $table->foreign('application_id')->references('id')->on('applications')->nullOnDelete();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['advt_code', 'status']);
        });

        // Application status history (immutable records for auditability)
        Schema::create('application_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->foreign('changed_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('changed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_status_histories');
        Schema::dropIfExists('challans');
        Schema::dropIfExists('applications');
    }
};