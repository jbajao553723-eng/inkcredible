<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('loan_id')->constrained()->nullOnDelete();
            $table->string('reference')->nullable()->unique()->after('amount');
            $table->string('currency', 3)->default('PHP')->after('reference');
            $table->string('paymongo_session_id')->nullable()->unique()->after('provider_reference');
            $table->string('paymongo_payment_id')->nullable()->unique()->after('paymongo_session_id');
            $table->string('webhook_event_id')->nullable()->after('paid_at');
        });

        Schema::create('paymongo_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('event_type');
            $table->boolean('livemode')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paymongo_webhook_events');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['reference']);
            $table->dropUnique(['paymongo_session_id']);
            $table->dropUnique(['paymongo_payment_id']);
            $table->dropColumn([
                'user_id',
                'reference',
                'currency',
                'paymongo_session_id',
                'paymongo_payment_id',
                'webhook_event_id',
            ]);
        });
    }
};