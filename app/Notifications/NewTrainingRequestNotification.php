<?php

namespace App\Notifications;

use App\Models\TrainingRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTrainingRequestNotification extends Notification
{
    public function __construct(public TrainingRequest $request) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $leaders = $this->request->leaders_participation ? 'Sim' : 'Não';
        $classType = $this->request->class_type === 'EXCLUSIVA' ? 'Exclusiva' : 'Aberta';
        $institutionLabel = $this->request->isMunicipality() ? 'Município' : 'Órgão/Secretaria';

        return (new MailMessage)
            ->subject('[Solicitações EGPCE] Nova solicitação de curso')
            ->markdown('mail.requests.standard', [
                'title' => 'Nova solicitação de curso',
                'intro' => 'Uma nova solicitação de curso foi registrada no sistema.',
                'details' => [
                    'Protocolo' => $this->request->protocol,
                    'Tipo da solicitação' => $this->request->scope_label,
                    $institutionLabel => $this->request->institution_name,
                    'Tipo de evento' => $this->request->event_type,
                    'Tipo de curso/formação' => $this->request->training_type,
                    'Tipo de turma' => $classType,
                    'Participantes' => $this->request->participants_count,
                    'Participação de lideranças' => $leaders,
                    'Responsável' => $this->request->requester_name . ' (' . $this->request->requester_email . ')',
                ],
                'actionText' => 'Acessar painel administrativo',
                'actionUrl' => url('/admin'),
                'footer' => 'Recomendação: acompanhe o status no painel para dar encaminhamento à demanda.',
            ]);
    }
}
