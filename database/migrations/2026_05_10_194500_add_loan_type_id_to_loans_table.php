<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('loan_type_id')
                ->nullable()
                ->after('user_id')
                ->constrained('loan_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['loan_type_id']);
            $table->dropColumn('loan_type_id');
        });
    }
};
