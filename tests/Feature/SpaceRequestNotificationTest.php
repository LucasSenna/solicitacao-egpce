<?php

namespace Tests\Feature;

use App\Infra\Spaces;
use App\Livewire\Requests\Space\Create;
use App\Notifications\NewSpaceRequestNotification;
use App\Notifications\SpaceRequestReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SpaceRequestNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('requests.space_notify_emails', [
            'josinelde.coelho@egp.ce.gov.br',
            'rodrigo.lopes@egp.ce.gov.br',
            'joao.bastos@egp.ce.gov.br',
        ]);
    }

    public function test_it_sends_space_request_notifications_to_three_admin_recipients(): void
    {
        Storage::fake('public');
        Notification::fake();

        $spaceKey = Spaces::keys()[0] ?? null;

        $this->assertNotNull($spaceKey);

        Livewire::test(Create::class)
            ->set('termsAccepted', true)
            ->set('event_title', 'Capacitação em Gestão Pública')
            ->set('objective', 'Aprimorar competências dos servidores municipais')
            ->set('target_audience', 'Gestores e equipes técnicas')
            ->set('start_date', now()->addDays(10)->toDateString())
            ->set('end_date', now()->addDays(11)->toDateString())
            ->set('time_slot', 'manha')
            ->set('participants_quantity', 40)
            ->set('institution_name', 'outro')
            ->set('institution_other', 'Prefeitura Municipal de Teste')
            ->set('responsible_name', 'Carlos Responsável')
            ->set('responsible_role', 'Coordenador')
            ->set('responsible_email', 'responsavel@example.com')
            ->set('responsible_phone', '(85) 99999-0000')
            ->set('selectedSpaces', [$spaceKey])
            ->set('responsibility_term', UploadedFile::fake()->create('termo.pdf', 200, 'application/pdf'))
            ->call('submit')
            ->assertHasNoErrors();

        Notification::assertSentOnDemand(NewSpaceRequestNotification::class, function ($notification, $channels, $notifiable, $locale = null): bool {
            $route = $notifiable->routeNotificationFor('mail', $notification);
            $emails = is_array($route) ? array_values($route) : [$route];
            sort($emails);

            return $emails === [
                'joao.bastos@egp.ce.gov.br',
                'josinelde.coelho@egp.ce.gov.br',
                'rodrigo.lopes@egp.ce.gov.br',
            ];
        });

        Notification::assertSentOnDemand(SpaceRequestReceivedNotification::class, function ($notification, $channels, $notifiable, $locale = null): bool {
            $route = $notifiable->routeNotificationFor('mail', $notification);

            return $route === 'responsavel@example.com';
        });
    }
}
