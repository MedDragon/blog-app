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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Поліморфні поля: створить likeable_id та likeable_type
            $table->morphs('likeable');
            $table->timestamps();

            // Унікальний індекс, щоб один юзер не міг лайкнути один об'єкт двічі
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
