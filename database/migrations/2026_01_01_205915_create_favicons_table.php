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
        Schema::create('favicons', function (Blueprint $table) {
            $table->id();
            $table->string('apple_touch_icon')->nullable();
            $table->string('favicon_icon')->nullable();
            $table->string('favicon_svg')->nullable();
            $table->string('favicon_96x96')->nullable();
            $table->string('manifest_192x192')->nullable();
            $table->string('manifest_512x512')->nullable();
            $table->string('site_manifest')->nullable();
            $table->string('env')->default(config('app.env'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favicons');
    }
};
