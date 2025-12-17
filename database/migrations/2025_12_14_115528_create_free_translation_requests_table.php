<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('free_translation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('source_language');
            $table->string('target_language');
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable(); // مسار الملف المرفوع
            $table->string('status')->default('new'); // new, in_progress, done
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('free_translation_requests');
    }
};
