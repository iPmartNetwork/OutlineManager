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
        Schema::create('access_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->references('id')->on('servers')->cascadeOnDelete();
            $table->unsignedInteger('api_id');
            $table->string('name');
            $table->string('password');
            $table->string('method');
            $table->string('access_url');
            $table->unsignedInteger('port');
            $table->unsignedBigInteger('data_limit')->nullable();
            $table->unsignedBigInteger('data_usage')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_keys');
    }
};
