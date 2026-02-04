<?php

use App\Models\Artist;
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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Artist::class)->constrained()->onDelete('cascade');
            $table->unsignedInteger('external_site_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle_one')->nullable();
            $table->string('subtitle_two')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt_text')->nullable();
            $table->text('body');
            $table->dateTime('publish_date');
            $table->boolean('from_api')->default(false);
            $table->string('env')->default(config('app.env'));
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
