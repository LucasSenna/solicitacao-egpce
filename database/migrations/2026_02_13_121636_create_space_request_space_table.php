<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('space_request_space', function (Blueprint $table) {
            $table->foreignId('space_request_id')->constrained('space_requests')->cascadeOnDelete();
            $table->foreignId('space_id')->constrained('spaces')->cascadeOnDelete();
            $table->primary(['space_request_id', 'space_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('space_request_space');
    }
};
