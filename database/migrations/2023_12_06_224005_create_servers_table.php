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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('api_url');
            $table->string('api_cert_sha256');
            $table->string('api_id');
            $table->string('name');
            $table->string('version', 10);
            $table->string('hostname_or_ip');
            $table->string('hostname_for_new_access_keys');
            $table->unsignedInteger('port_for_new_access_keys');
            $table->unsignedBigInteger('total_data_usage')->nullable();
            $table->boolean('is_metrics_enabled');
            $table->boolean('is_available')->default(true);
            $table->timestamp('api_created_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
