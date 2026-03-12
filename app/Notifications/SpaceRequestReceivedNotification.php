<?php

namespace App\Notifications;

use App\Models\SpaceRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SpaceRequestReceivedNotification extends Notification
{
    public function __construct(public SpaceRequest $request) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $year = optional($this->request->created_at)->format('Y') ?: now()->format('Y');
        $protocol = 'ESP-' . $year . '-' . str_pad((string) $this->request->id, 6, '0', STR_PAD_LEFT);

        return (new MailMessage)
            ->subject('[Solicitações EGPCE] Solicitação de cessão de espaço recebida - Protocolo ' . $protocol)
            ->markdown('mail.requests.standard', [
                'title' => 'Solicitação de cessão de espaço recebida',
                'intro' => 'Recebemos sua solicitação com sucesso.',
                'details' => [
                    'Protocolo' => $protocol,
                    'Órgão/Secretaria' => $this->request->institution_name,
                    'Evento' => $this->request->event_title,
                ],
                'actionText' => 'Voltar para o site',
                'actionUrl' => url('/'),
                'footer' => 'A equipe da EGPCE analisará os dados e enviará retorno em breve. Guarde este protocolo para acompanhamento.',
            ]);
    }
}
