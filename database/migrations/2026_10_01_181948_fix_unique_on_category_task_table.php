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
        Schema::table('category_task', function (Blueprint $table) {
            // نفس المهمة ما بتنضاف لنفس التصنيف مرتين، بس المهمة بيصير إلها أكثر من تصنيف
            $table->unique(['task_id', 'category_id']);
            $table->index('category_id');
        });

        Schema::table('category_task', function (Blueprint $table) {
            $table->dropUnique(['task_id']);
            $table->dropUnique(['category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_task', function (Blueprint $table) {
            $table->unique('task_id');
            $table->unique('category_id');
        });

        Schema::table('category_task', function (Blueprint $table) {
            $table->dropUnique(['task_id', 'category_id']);
            $table->dropIndex(['category_id']);
        });
    }
};
