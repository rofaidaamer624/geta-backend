<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {

            $table->dropColumn(['short_description', 'description']);

            $table->string('short_description_ar')->nullable()->after('name_en');
            $table->string('short_description_en')->nullable()->after('short_description_ar');

            $table->text('description_ar')->nullable()->after('short_description_en');
            $table->text('description_en')->nullable()->after('description_ar');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {

            $table->dropColumn([
                'short_description_ar',
                'short_description_en',
                'description_ar',
                'description_en',
            ]);

            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
        });
    }
};

