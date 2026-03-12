<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('training_requests', function (Blueprint $table) {
            $table->id();

            // Protocolo: 20260001, 20260002...
            $table->string('protocol', 8)->unique();

            $table->string('institution_name'); // órgão/secretaria
            $table->string('holder_name'); // titular
            $table->string('holder_role'); // cargo titular

            $table->string('requester_name'); // responsável
            $table->string('requester_role'); // cargo responsável
            $table->string('requester_phone'); // telefone
            $table->string('requester_email'); // email

            $table->string('event_type'); // CURSO, PALESTRA...
            $table->unsignedInteger('participants_count');
            $table->string('training_type'); // tipo de formação
            $table->string('class_type'); // ABERTA | EXCLUSIVA
            $table->text('target_audience'); // público participante

            $table->boolean('leaders_participation'); // sim/nao
            $table->text('objectives'); // objetivos
            $table->text('content_expectation'); // expectativa

            $table->string('request_letter_path'); // upload ofício

            // status administrativo
            $table->enum('status', ['nao_iniciado', 'em_andamento', 'realizado', 'nao_realizado'])->default('nao_iniciado');
            $table->text('admin_notes')->nullable();

            // aceite do termo
            $table->boolean('terms_accepted')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_requests');
    }
};
