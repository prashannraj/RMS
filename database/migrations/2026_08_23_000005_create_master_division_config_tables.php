<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master division (used for service/group/sub-group classification)
        Schema::create('master_divisions', function (Blueprint $table) {
            $table->id();
            $table->string('division_name')->nullable();
            $table->string('remarks')->nullable();
            $table->string('master_division_type')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('master_divisions')->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Master configuration (post-level config per office)
        Schema::create('master_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->restrictOnDelete();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->foreign('office_id')->references('id')->on('organizations')->nullOnDelete();
            $table->string('config_key')->nullable();
            $table->text('config_value')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_configurations');
        Schema::dropIfExists('master_divisions');
    }
};