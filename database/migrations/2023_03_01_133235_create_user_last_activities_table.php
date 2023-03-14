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
        $connection = config('auth-last-activity.connection');
        Schema::connection($connection)->create('auth_last_activities', function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable', 'authenticatable_index');
            $table->text('last_activity_url');
            $table->text('previous_url')->nullable();
            $table->string('user_agent');
            $table->ipAddress('ip_address');
            $table->json('headers');
            $table->boolean('is_mobile');
            $table->enum('request_source', ['web', 'api']);
            $table->dateTime('last_activity_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = config('auth-last-activity.connection');
        Schema::connection($connection)->dropIfExists('auth_last_activities');
    }
};
