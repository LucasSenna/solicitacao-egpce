<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('space_requests', function (Blueprint $table) {
            $table->id();

            // dados do evento
            $table->string('institution_name');
            $table->string('responsible_name');
            $table->string('responsible_role');
            $table->string('responsible_email');
            $table->string('responsible_phone');

            $table->string('event_title');
            $table->text('objective');
            $table->text('target_audience');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('time_slot', ['manha', 'tarde', 'manha_tarde']);
            $table->unsignedInteger('participants_quantity');

            $table->text('general_notes')->nullable();
            $table->json('selected_spaces')->nullable();
            $table->json('selected_spaces_snapshot')->nullable();

            // termo / anexo
            $table->timestamp('accepted_terms_at')->nullable();
            $table->string('responsibility_term_path')->nullable();

            // fluxo
            $table->enum('status', ['pendente', 'aprovado', 'recusado', 'cancelado'])->default('pendente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('space_requests');
    }
};
