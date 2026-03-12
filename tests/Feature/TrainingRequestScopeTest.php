<?php

namespace Tests\Feature;

use App\Filament\Pages\Reports;
use App\Livewire\Requests\Training\Create;
use App\Models\TrainingRequest;
use App\Models\User;
use App\Notifications\NewTrainingRequestNotification;
use App\Notifications\TrainingRequestReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class TrainingRequestScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('requests.training_notify_emails', [
            TrainingRequest::SCOPE_STATE => [
                'josinelde.coelho@egp.ce.gov.br',
                'rodrigo.lopes@egp.ce.gov.br',
                'joao.bastos@egp.ce.gov.br',
            ],
            TrainingRequest::SCOPE_MUNICIPALITY => ['rodrigo.lopes@egp.ce.gov.br', 'joao.bastos@egp.ce.gov.br'],
        ]);
    }

    public function test_it_creates_a_state_training_request_with_state_scope_and_state_email_recipients(): void
    {
        Storage::fake('public');
        Notification::fake();

        Livewire::test(Create::class, ['scope' => TrainingRequest::SCOPE_STATE])
            ->set('termsAccepted', true)
            ->set('institution_name', 'SECRETARIA DA EDUCAÇÃO (SEDUC)')
            ->set('holder_name', 'Maria Titular')
            ->set('holder_role', 'Secretária')
            ->set('requester_name', 'Ana Solicitante')
            ->set('requester_role', 'Coordenadora')
            ->set('requester_phone', '(85) 99999-9999')
            ->set('requester_email', 'ana@example.com')
            ->set('event_type', 'CURSO')
            ->set('participants_count', 40)
            ->set('training_type', 'Presencial')
            ->set('class_type', 'ABERTA')
            ->set('target_audience', 'Servidores da área administrativa')
            ->set('leaders_participation', 'SIM')
            ->set('objectives', 'Alinhar fluxos de trabalho e melhorar indicadores operacionais')
            ->set('content_expectation', 'Conteúdo prático sobre processos e gestão de equipes')
            ->set('request_letter', UploadedFile::fake()->create('oficio.pdf', 200, 'application/pdf'))
            ->call('submit')
            ->assertHasNoErrors();

        $request = TrainingRequest::query()->first();

        $this->assertNotNull($request);
        $this->assertSame(TrainingRequest::SCOPE_STATE, $request->request_scope);
        $this->assertTrue($request->isState());
        $this->assertSame('Estado', $request->scope_label);

        Notification::assertSentOnDemand(NewTrainingRequestNotification::class, function ($notification, $channels, $notifiable, $locale = null): bool {
            $route = $notifiable->routeNotificationFor('mail', $notification);
            $emails = is_array($route) ? array_values($route) : [$route];

            sort($emails);

            return $emails === [
                'joao.bastos@egp.ce.gov.br',
                'josinelde.coelho@egp.ce.gov.br',
                'rodrigo.lopes@egp.ce.gov.br',
            ];
        });

        Notification::assertSentOnDemand(TrainingRequestReceivedNotification::class, function ($notification, $channels, $notifiable, $locale = null): bool {
            $route = $notifiable->routeNotificationFor('mail', $notification);

            return $route === 'ana@example.com';
        });
    }

    public function test_it_creates_a_municipality_training_request_with_municipality_scope_and_two_admin_recipients(): void
    {
        Storage::fake('public');
        Notification::fake();

        Livewire::test(Create::class, ['scope' => TrainingRequest::SCOPE_MUNICIPALITY])
            ->set('termsAccepted', true)
            ->set('institution_name', 'Fortaleza')
            ->set('holder_name', 'Carlos Titular')
            ->set('holder_role', 'Prefeito')
            ->set('requester_name', 'João Solicitante')
            ->set('requester_role', 'Gestor de Pessoas')
            ->set('requester_phone', '(85) 98888-7777')
            ->set('requester_email', 'joao@example.com')
            ->set('event_type', 'WORKSHOP')
            ->set('participants_count', 55)
            ->set('training_type', 'Híbrido')
            ->set('class_type', 'EXCLUSIVA')
            ->set('target_audience', 'Servidores municipais da secretaria de administração')
            ->set('leaders_participation', 'NAO')
            ->set('objectives', 'Fortalecer planejamento e governança para equipes municipais')
            ->set('content_expectation', 'Metodologias de gestão pública e indicadores de desempenho')
            ->set('request_letter', UploadedFile::fake()->create('oficio.pdf', 200, 'application/pdf'))
            ->call('submit')
            ->assertHasNoErrors();

        $request = TrainingRequest::query()->first();

        $this->assertNotNull($request);
        $this->assertSame(TrainingRequest::SCOPE_MUNICIPALITY, $request->request_scope);
        $this->assertTrue($request->isMunicipality());
        $this->assertSame('Município', $request->scope_label);

        Notification::assertSentOnDemand(NewTrainingRequestNotification::class, function ($notification, $channels, $notifiable, $locale = null): bool {
            $route = $notifiable->routeNotificationFor('mail', $notification);
            $emails = is_array($route) ? array_values($route) : [$route];
            sort($emails);

            return $emails === ['joao.bastos@egp.ce.gov.br', 'rodrigo.lopes@egp.ce.gov.br'];
        });

        Notification::assertSentOnDemand(TrainingRequestReceivedNotification::class, function ($notification, $channels, $notifiable, $locale = null): bool {
            $route = $notifiable->routeNotificationFor('mail', $notification);

            return $route === 'joao@example.com';
        });
    }

    public function test_reports_filters_training_requests_by_state_and_municipality_scope(): void
    {
        $this->actingAs(User::factory()->create([
            'is_admin' => true,
            'admin_profile' => User::ADMIN_PROFILE_FULL_ACCESS,
        ]));

        $stateRequestA = $this->createTrainingRequest([
            'protocol' => '20260001',
            'request_scope' => TrainingRequest::SCOPE_STATE,
            'institution_name' => 'SECRETARIA DA EDUCAÇÃO (SEDUC)',
        ]);

        $stateRequestB = $this->createTrainingRequest([
            'protocol' => '20260002',
            'request_scope' => TrainingRequest::SCOPE_STATE,
            'institution_name' => 'SECRETARIA DA SAÚDE (SESA)',
        ]);

        $municipalityRequestA = $this->createTrainingRequest([
            'protocol' => '20260003',
            'request_scope' => TrainingRequest::SCOPE_MUNICIPALITY,
            'institution_name' => 'Fortaleza',
        ]);

        $municipalityRequestB = $this->createTrainingRequest([
            'protocol' => '20260004',
            'request_scope' => TrainingRequest::SCOPE_MUNICIPALITY,
            'institution_name' => 'Sobral',
        ]);

        Livewire::test(Reports::class)
            ->set('data.type', 'training')
            ->set('data.training_scope', TrainingRequest::SCOPE_STATE)
            ->set('data.state_institution_name', 'SECRETARIA DA EDUCAÇÃO (SEDUC)')
            ->assertSee($stateRequestA->protocol)
            ->assertDontSee($stateRequestB->protocol)
            ->assertDontSee($municipalityRequestA->protocol)
            ->assertDontSee($municipalityRequestB->protocol);

        Livewire::test(Reports::class)
            ->set('data.type', 'training')
            ->set('data.training_scope', TrainingRequest::SCOPE_MUNICIPALITY)
            ->set('data.municipality_name', 'Sobral')
            ->assertSee($municipalityRequestB->protocol)
            ->assertDontSee($municipalityRequestA->protocol)
            ->assertDontSee($stateRequestA->protocol)
            ->assertDontSee($stateRequestB->protocol);
    }

    private function createTrainingRequest(array $overrides = []): TrainingRequest
    {
        static $counter = 100;
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
            'target_audience' => 'Público de teste para validação de relatório',
            'leaders_participation' => true,
            'objectives' => 'Objetivo de teste para criação de registros em relatório',
            'content_expectation' => 'Expectativa de conteúdo para validar filtros por escopo',
            'request_letter_path' => 'training-requests/teste/oficio.pdf',
            'status' => 'nao_iniciado',
            'terms_accepted' => true,
        ], $overrides));
    }
}
