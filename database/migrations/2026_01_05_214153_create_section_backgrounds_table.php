<?php

use App\Models\Section;
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
        Schema::create('section_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Section::class)->constrained()->onDelete('cascade');
            $table->string('type')->default('image');
            $table->string('color')->default('white');
            $table->string('image')->nullable();
            $table->string('image_extension')->nullable();
            $table->string('video')->nullable();
            $table->string('image_position')->default('center');
            $table->string('image_type')->default('fill');
            $table->boolean('is_mobile')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_backgrounds');
    }
};
