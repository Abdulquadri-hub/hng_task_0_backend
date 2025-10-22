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
        Schema::create('string_models', function (Blueprint $table) {
           $table->string('id')->primary(); 
            $table->text('value');
            $table->integer('length');
            $table->boolean('is_palindrome');
            $table->integer('unique_characters');
            $table->integer('word_count');
            $table->string('sha256_hash')->unique();
            $table->json('character_frequency_map');
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('is_palindrome');
            $table->index('length');
            $table->index('word_count');
            $table->index('value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('string_models');
    }
};
