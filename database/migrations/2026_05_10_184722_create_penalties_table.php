<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_schedule_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type')->default('late_payment'); // late_payment, additional_fee, etc.
            $table->decimal('amount', 10, 2);
            $table->decimal('rate_percentage', 5, 2)->nullable(); // e.g., 2% per day
            $table->integer('days_overdue')->default(0);
            $table->date('applied_date');
            $table->text('reason')->nullable();
            $table->string('status')->default('active'); // active, waived, paid
            $table->timestamp('waived_at')->nullable();
            $table->foreignId('waived_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('waiver_reason')->nullable();
            $table->timestamps();

            $table->index(['loan_id', 'status']);
            $table->index(['applied_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};
