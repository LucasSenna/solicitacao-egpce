<?php

namespace Tests\Feature;

use App\Filament\Pages\Reports;
use App\Models\TrainingRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProfileAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_municipality_only_admin_can_only_access_municipality_training_requests(): void
    {
        $municipalityAdmin = User::factory()->create([
            'is_admin' => true,
            'admin_profile' => User::ADMIN_PROFILE_MUNICIPALITY_ONLY,
        ]);

        $stateTraining = $this->createTrainingRequest([
            'protocol' => '20260001',
            'request_scope' => TrainingRequest::SCOPE_STATE,
            'institution_name' => 'SECRETARIA DA EDUCAÇÃO (SEDUC)',
        ]);

        $municipalityTraining = $this->createTrainingRequest([
            'protocol' => '20260002',
            'request_scope' => TrainingRequest::SCOPE_MUNICIPALITY,
            'institution_name' => 'Fortaleza',
        ]);

        $visibleCount = $municipalityAdmin
            ->applyTrainingRequestsScope(TrainingRequest::query())
            ->count();

        $this->assertTrue($municipalityAdmin->canAccessTrainingRequest($municipalityTraining));
        $this->assertFalse($municipalityAdmin->canAccessTrainingRequest($stateTraining));
        $this->assertFalse($municipalityAdmin->canAccessSpaceRequests());
        $this->assertSame(1, $visibleCount);

        $this->actingAs($municipalityAdmin);
        $this->assertFalse(Reports::canAccess());
    }

    public function test_full_access_admin_can_access_all_request_types(): void
    {
        $fullAdmin = User::factory()->create([
            'is_admin' => true,
            'admin_profile' => User::ADMIN_PROFILE_FULL_ACCESS,
        ]);

        $stateTraining = $this->createTrainingRequest([
            'protocol' => '20260011',
            'request_scope' => TrainingRequest::SCOPE_STATE,
            'institution_name' => 'SECRETARIA DA SAÚDE (SESA)',
        ]);

        $municipalityTraining = $this->createTrainingRequest([
            'protocol' => '20260012',
            'request_scope' => TrainingRequest::SCOPE_MUNICIPALITY,
            'institution_name' => 'Sobral',
        ]);

        $visibleCount = $fullAdmin
            ->applyTrainingRequestsScope(TrainingRequest::query())
            ->count();

        $this->assertTrue($fullAdmin->canManageAllRequestTypes());
        $this->assertTrue($fullAdmin->canAccessTrainingRequest($stateTraining));
        $this->assertTrue($fullAdmin->canAccessTrainingRequest($municipalityTraining));
        $this->assertTrue($fullAdmin->canAccessSpaceRequests());
        $this->assertSame(2, $visibleCount);

        $this->actingAs($fullAdmin);
        $this->assertTrue(Reports::canAccess());
    }

    private function createTrainingRequest(array $overrides = []): TrainingRequest
    {
        static $counter = 300;
        $counter++;

        return TrainingRequest::query()->create(array_merge([
            'protocol' => '2026' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'request_scope' => TrainingRequest::SCOPE_STATE,
            'institution_name' => 'SECRETARIA TESTE',
            'holder_name' => 'Titular Teste',
            'holder_role' => 'Gestor',
            'requester_name' => 'Solicitante Teste',
            'requester_role' => 'Coordenador',
            'requester_phone' => '(85) 90000-0000',
            'requester_email' => 'teste@example.com',
            'event_type' => 'CURSO',
            'participants_count' => 30,
            'training_type' => 'Presencial',
            'class_type' => 'ABERTA',
            'target_audience' => 'Público de teste para validação de perfil administrativo',
            'leaders_participation' => true,
            'objectives' => 'Objetivo de teste para validação de perfis administrativos',
            'content_expectation' => 'Expectativa de conteúdo para validação de permissões',
            'request_letter_path' => 'training-requests/teste/oficio.pdf',
            'status' => 'nao_iniciado',
            'terms_accepted' => true,
        ], $overrides));
    }
}
