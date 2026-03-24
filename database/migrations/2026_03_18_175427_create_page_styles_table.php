<?php

use App\Models\Artist;
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

        $positions = [
            'object-top',
            'object-center',
            'object-bottom',
            'object-left',
            'object-right',
            'object-top-left',
            'object-top-right',
            'object-bottom-left',
            'object-bottom-right',
            'object-left-third',
            'object-left-fourth',
            'object-right-third',
            'object-right-fourth',
        ];

        Schema::create('page_styles', function (Blueprint $table) use ($positions) {
            $table->id();
            $table->foreignIdFor(Artist::class)->constrained()->onDelete('cascade');
            $table->string('image')->nullable();
            $table->enum('image_position', $positions)->default('object-center');
            $table->string('image_custom_position_x')->nullable();
            $table->string('image_custom_position_y')->nullable();
            $table->string('image_extension')->nullable();
            $table->string('device_type');
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
        Schema::dropIfExists('page_styles');
    }
};
