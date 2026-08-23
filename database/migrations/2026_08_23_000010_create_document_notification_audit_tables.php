<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Document metadata (separate from physical file storage)
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id');
            $table->foreign('candidate_id')->references('id')->on('candidates')->restrictOnDelete();
            $table->string('document_type'); // citizenship, transcript, signature, photo, samuha_doc, etc.
            $table->string('status')->default('pending');
            $table->dateTime('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['candidate_id', 'document_type']);
        });

        // File upload metadata (no BLOBs in DB)
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->foreign('document_id')->references('id')->on('documents')->restrictOnDelete();
            $table->string('disk');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('stored_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->default(0);
            $table->string('checksum')->nullable();
            $table->string('visibility')->default('private'); // private / public
            $table->timestamps();
        });

        // Notifications — Laravel-standard table already exists (
        // 2026_01_01_000004_create_notifications_table.php ). Add PPSC-specific
        // candidate/user-facing columns to it (user_id, title, channel, message).
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('title')->nullable()->after('type');
            $table->text('message')->nullable()->after('title');
            $table->string('channel')->default('mail')->after('message');
            $table->index(['user_id', 'read_at']);
        });
        // Audit logs (append-only)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('action'); // created, updated, verified, submitted, etc.
            $table->string('module');
            $table->string('record_type')->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('request_id')->nullable();
            $table->string('severity')->default('info');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['module', 'record_type', 'record_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('file_uploads');
        Schema::dropIfExists('documents');
    }
};