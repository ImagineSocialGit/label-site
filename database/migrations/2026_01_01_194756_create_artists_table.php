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
            $table->string('desktop_image')->nullable();
            $table->string('desktop_image_position')->nullable();
            $table->string('mobile_image')->nullable();
            $table->string('mobile_image_position')->nullable();
            $table->string('url')->nullable();
            $table->text('about')->nullable();
            $table->unsignedInteger('order');
            $table->string('token')->nullable();
            $table->boolean('requires_refresh')->default(false);
            $table->string('env')->default(config('app.env'));
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
