<?php

use App\Models\Music;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('music_links', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Music::class)->nullable()->constrained()->onDelete('cascade');
            $table->string('service');
            $table->string('link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('music_links');
    }
};
