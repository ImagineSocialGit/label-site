<?php

use App\Models\Artist;
use App\Models\Post;
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
        Schema::create('meta_data', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Artist::class)->constrained()->onDelete('cascade')->nullable();
            $table->foreignIdFor(Post::class)->constrained()->onDelete('cascade')->nullable();
            $table->string('linked_view');
            $table->string('title');
            $table->text('description');
            $table->string('env')->default(config('app.env'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_data');
    }
};
