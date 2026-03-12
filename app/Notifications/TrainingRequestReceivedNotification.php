<?php

namespace App\Notifications;

use App\Models\TrainingRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingRequestReceivedNotification extends Notification
{
    public function __construct(public TrainingRequest $request) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $institutionLabel = $this->request->isMunicipality() ? 'Município' : 'Órgão/Secretaria';

        return (new MailMessage)
            ->subject('[Solicitações EGPCE] Solicitação de curso recebida - Protocolo ' . $this->request->protocol)
            ->markdown('mail.requests.standard', [
                'title' => 'Solicitação de curso recebida',
                'intro' => 'Recebemos sua solicitação com sucesso.',
                'details' => [
                    'Protocolo' => $this->request->protocol,
                    'Tipo da solicitação' => $this->request->scope_label,
                    $institutionLabel => $this->request->institution_name,
                    'Tipo de curso/formação' => $this->request->training_type,
                ],
                'actionText' => 'Voltar para o site',
                'actionUrl' => url('/'),
                'footer' => 'A equipe da EGPCE analisará os dados e enviará retorno em breve. Guarde este protocolo para acompanhamento.',
            ]);
    }
}
