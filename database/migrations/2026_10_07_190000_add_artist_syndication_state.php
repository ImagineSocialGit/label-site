<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artists', function (Blueprint $table): void {
            $table->boolean('syndication_enabled')
                ->default(false)
                ->after('token');
            $table->string('syndication_revision', 64)
                ->nullable()
                ->after('syndication_enabled');
            $table->timestamp('syndication_last_attempt_at')
                ->nullable()
                ->after('syndication_revision');
            $table->timestamp('syndication_last_success_at')
                ->nullable()
                ->after('syndication_last_attempt_at');
            $table->text('syndication_last_error')
                ->nullable()
                ->after('syndication_last_success_at');
        });

        Schema::table('music', function (Blueprint $table): void {
            $table->string('source_key')
                ->nullable()
                ->after('external_site_id');
            $table->unique(
                ['artist_id', 'source_key'],
                'music_artist_source_key_unique',
            );
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->string('source_key')
                ->nullable()
                ->after('external_site_id');
            $table->unique(
                ['artist_id', 'source_key'],
                'posts_artist_source_key_unique',
            );
        });

        Schema::table('videos', function (Blueprint $table): void {
            $table->string('source_key')
                ->nullable()
                ->after('external_site_id');
            $table->boolean('is_featured')
                ->default(false)
                ->after('is_holiday');
            $table->unique(
                ['artist_id', 'source_key'],
                'videos_artist_source_key_unique',
            );
        });

        DB::table('artists')
            ->whereNotNull('url')
            ->where('url', '<>', '')
            ->whereNotNull('token')
            ->where('token', '<>', '')
            ->update([
                'syndication_enabled' => true,
            ]);
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table): void {
            $table->dropUnique('videos_artist_source_key_unique');
            $table->dropColumn([
                'source_key',
                'is_featured',
            ]);
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->dropUnique('posts_artist_source_key_unique');
            $table->dropColumn('source_key');
        });

        Schema::table('music', function (Blueprint $table): void {
            $table->dropUnique('music_artist_source_key_unique');
            $table->dropColumn('source_key');
        });

        Schema::table('artists', function (Blueprint $table): void {
            $table->dropColumn([
                'syndication_enabled',
                'syndication_revision',
                'syndication_last_attempt_at',
                'syndication_last_success_at',
                'syndication_last_error',
            ]);
        });
    }
};