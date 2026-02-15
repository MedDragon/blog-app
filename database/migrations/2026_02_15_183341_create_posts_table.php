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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Хто створив (вимога лаби)
            $table->string('title'); // Заголовок (вимога лаби)
            $table->text('content'); // Текст (вимога лаби)
            $table->string('source')->default('Source 1'); // Твій бекграунд для Zoho
            $table->string('external_id')->nullable(); // Твій бекграунд для Zoho
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
