<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->longText('admin_signature')->nullable()->after('signed_contract_original_name');
            $table->string('admin_signature_name')->nullable()->after('admin_signature');
            $table->timestamp('admin_signed_at')->nullable()->after('admin_signature_name');
            $table->foreignId('admin_signed_by')->nullable()->after('admin_signed_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_signed_by');
            $table->dropColumn(['admin_signature', 'admin_signature_name', 'admin_signed_at']);
        });
    }
};
