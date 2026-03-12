<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('space_request_equipment', function (Blueprint $table) {
            $table->foreignId('space_request_id')->constrained('space_requests')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->primary(['space_request_id', 'equipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('space_request_equipment');
    }
};
