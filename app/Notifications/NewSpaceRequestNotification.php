<?php

namespace App\Notifications;

use App\Models\SpaceRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSpaceRequestNotification extends Notification
{
    public function __construct(public SpaceRequest $request) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $turno = match ($this->request->time_slot) {
            'manha' => 'Manhã',
            'tarde' => 'Tarde',
            default => 'Manhã e Tarde',
        };

        $spaces = collect($this->request->selected_spaces_snapshot ?? [])
            ->map(function (array $space): string {
                $label = $space['label'] ?? ($space['key'] ?? 'Espaço');
                $capacity = $space['capacity'] ?? null;

                return filled($capacity) ? "{$label} ({$capacity} pessoas)" : $label;
            })
            ->values()
            ->all();

        if (empty($spaces)) {
            $spaces = ['Não informado'];
        }

        return (new MailMessage)
            ->subject('[Solicitações EGPCE] Nova solicitação de cessão de espaço')
            ->markdown('mail.requests.standard', [
                'title' => 'Nova solicitação de cessão de espaço',
                'intro' => 'Uma nova solicitação de cessão de espaços foi registrada no sistema.',
                'details' => [
                    'ID da solicitação' => (string) $this->request->id,
                    'Órgão/Secretaria' => $this->request->institution_name,
                    'Evento' => $this->request->event_title,
                    'Período' => $this->request->start_date->format('d/m/Y') . ' a ' . $this->request->end_date->format('d/m/Y'),
                    'Turno' => $turno,
                    'Responsável' => $this->request->responsible_name . ' (' . $this->request->responsible_email . ')',
                ],
                'listTitle' => 'Espaços solicitados:',
                'listItems' => $spaces,
                'actionText' => 'Acessar painel administrativo',
                'actionUrl' => url('/admin'),
                'footer' => 'Recomendação: valide disponibilidade e siga o fluxo de aprovação no painel.',
            ]);
    }
}
