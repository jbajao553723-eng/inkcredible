<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stored_files', function (Blueprint $table): void {
            $table->id();
            $table->string('disk', 32);
            $table->string('path', 512);
            $table->longText('contents_base64');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size');
            $table->string('visibility', 16)->default('private');
            $table->timestamps();

            $table->unique(['disk', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stored_files');
    }
};
