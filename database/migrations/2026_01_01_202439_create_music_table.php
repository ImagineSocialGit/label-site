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
        Schema::create('music', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Artist::class)->constrained()->onDelete('cascade');
            $table->foreignId('staging_music_id')->nullable()->references('id')->on('music');
            $table->unsignedInteger('external_site_id')->nullable();
            $table->string('title');
            $table->string('artwork');
            $table->string('artwork_alt_text')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_image_alt_text')->nullable();
            $table->boolean('include_in_banner')->default(false);
            $table->boolean('is_holiday_release')->default(false);
            $table->string('link')->nullable();
            $table->date('release_date');
            $table->date('presave_date')->nullable();
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
        Schema::dropIfExists('music');
    }
};
