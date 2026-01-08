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
        Schema::create('content_styles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Section::class)->constrained()->onDelete('cascade');
            $table->enum('height', ['xs', 'small', 'medium', 'large', 'xl', '2xl', '1/2', '3/4', 'screen'])->default('medium');
            $table->enum('width', ['xs', 'small', 'medium', 'large', 'xl', '2xl', '1/2', '2/3', 'screen'])->default('medium');
            $table->string('left_pad')->default('none');
            $table->string('right_pad')->default('none');
            $table->string('top_pad')->default('none');
            $table->string('bottom_pad')->default('none');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_styles');
    }
};
