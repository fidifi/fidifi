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
        Schema::table('forums', function (Blueprint $table) {
            $table->foreignId('last_post_id')->nullable()->after('posts_count')->constrained('posts')->nullOnDelete();
        });
        
        Schema::table('threads', function (Blueprint $table) {
            $table->foreignId('last_post_id')->nullable()->after('posts_count')->constrained('posts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            $table->dropForeign(['last_post_id']);
            $table->dropColumn('last_post_id');
        });
        
        Schema::table('threads', function (Blueprint $table) {
            $table->dropForeign(['last_post_id']);
            $table->dropColumn('last_post_id');
        });
    }
};
