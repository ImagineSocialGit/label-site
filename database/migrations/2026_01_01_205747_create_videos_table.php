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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Artist::class)->constrained()->onDelete('cascade');
            $table->foreignId('staging_video_id')->references('id')->on('videos')->nullable();
            $table->string('service');
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('link');
            $table->dateTime('released');
            $table->boolean('is_holiday')->default(false);
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
        Schema::dropIfExists('videos');
    }
};
