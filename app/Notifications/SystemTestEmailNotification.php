<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemTestEmailNotification extends Notification
{
    public function __construct(
        public string $context = 'Sistema de Solicitações EGPCE',
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[Solicitações EGPCE] Teste de e-mail')
            ->markdown('mail.requests.standard', [
                'title' => 'Teste de envio realizado com sucesso!',
                'details' => [
                    'Contexto' => $this->context,
                    'Data/Hora' => now()->format('d/m/Y H:i:s'),
                ],
                'footer' => 'Se você recebeu este e-mail, a configuração SMTP está funcionando.',
            ]);
    }
}
