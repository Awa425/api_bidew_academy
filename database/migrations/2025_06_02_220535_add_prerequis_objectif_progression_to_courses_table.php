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
        Schema::table('courses', function (Blueprint $table) {
            $table->text('prerequis')->nullable()->after('description');
            $table->text('objectif')->nullable()->after('prerequis');
            $table->unsignedTinyInteger('progression')->default(0)->after('objectif'); // 0 à 100%
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['prerequis', 'objectif', 'progression']);
        });
    }
};
