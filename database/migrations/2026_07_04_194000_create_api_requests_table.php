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
        Schema::create('api_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('request_id')->nullable()->unique();
            $table->string('method');
            $table->string('endpoint');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('request_headers')->nullable();
            $table->json('request_payload')->nullable();
            $table->integer('status_code')->nullable();
            $table->json('response_body')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_requests');
    }
};
