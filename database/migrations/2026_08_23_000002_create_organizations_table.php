<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Requesting offices / PSC organizations (referenced by requisitions, master_configurations)
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('organizations')->restrictOnDelete();
            $table->string('organization_name_np')->nullable();
            $table->string('organization_name_en')->nullable();
            $table->string('organization_code')->nullable()->unique();
            $table->boolean('can_schedule_exam')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};