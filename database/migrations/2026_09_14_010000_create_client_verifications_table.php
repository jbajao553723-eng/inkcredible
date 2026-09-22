<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->string('employment_status', 40);
            $table->string('company_name')->nullable();
            $table->string('job_title')->nullable();
            $table->decimal('monthly_income', 12, 2);
            $table->unsignedInteger('employment_length_months')->default(0);
            $table->string('source_of_income');
            $table->string('valid_id_type');
            $table->text('valid_id_number');
            $table->string('valid_id_path');
            $table->string('selfie_with_id_path');
            $table->text('additional_information')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_verifications');
    }
};
