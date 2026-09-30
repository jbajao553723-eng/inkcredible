<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 80)->index();
            $table->string('description');
            $table->string('route')->nullable();
            $table->string('method', 10)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->timestamp('contract_sent_at')->nullable()->after('approved_at');
            $table->timestamp('contract_signed_at')->nullable()->after('contract_sent_at');
            $table->string('contract_signature_name')->nullable()->after('contract_signed_at');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['contract_sent_at', 'contract_signed_at', 'contract_signature_name']);
        });

        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
    }
};
