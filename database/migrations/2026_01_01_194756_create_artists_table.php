<?php

use App\Models\Label;
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

        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Label::class)->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('url')->nullable();
            $table->text('about')->nullable();
            $table->unsignedInteger('order');
            $table->string('token')->nullable();
            $table->boolean('music_requires_refresh')->default(true);
            $table->boolean('posts_requires_refresh')->default(true);
            $table->boolean('videos_requires_refresh')->default(true);
            $table->boolean('design_requires_refresh')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
