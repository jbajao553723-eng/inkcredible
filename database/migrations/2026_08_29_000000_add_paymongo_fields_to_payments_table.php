<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('method')->default('cash')->change();
            $table->string('status')->default('pending')->change();
            $table->string('provider')->nullable()->after('method');
            $table->string('provider_reference')->nullable()->unique()->after('provider');
            $table->text('checkout_url')->nullable()->after('provider_reference');
            $table->timestamp('paid_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['provider_reference']);
            $table->dropColumn(['provider', 'provider_reference', 'checkout_url', 'paid_at']);
        });
    }
};