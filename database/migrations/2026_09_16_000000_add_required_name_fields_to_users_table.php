<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 100)->default('')->after('name');
            $table->string('last_name', 100)->default('')->after('first_name');
        });

        DB::table('users')->orderBy('id')->each(function (object $user): void {
            $parts = preg_split('/\s+/', trim((string) $user->name), 2);
            $firstName = $parts[0] ?? 'User';
            $lastName = $parts[1] ?? $firstName;

            DB::table('users')->where('id', $user->id)->update([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'contact_number' => $user->contact_number ?: 'Not provided',
                'age' => $user->age ?: 18,
                'address' => $user->address ?: 'Not provided',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('contact_number', 30)->nullable(false)->change();
            $table->unsignedInteger('age')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('contact_number', 30)->nullable()->change();
            $table->integer('age')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
