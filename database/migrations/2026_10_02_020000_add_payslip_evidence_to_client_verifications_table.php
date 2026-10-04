<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_verifications', function (Blueprint $table) {
            $table->string('payslip_path')->nullable()->after('selfie_with_id_path');
            $table->timestamp('payslip_uploaded_at')->nullable()->after('payslip_path');
            $table->timestamp('payslip_verified_at')->nullable()->after('payslip_uploaded_at');
        });
    }

    public function down(): void
    {
        Schema::table('client_verifications', function (Blueprint $table) {
            $table->dropColumn(['payslip_path', 'payslip_uploaded_at', 'payslip_verified_at']);
        });
    }
};
