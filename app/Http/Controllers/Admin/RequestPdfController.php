<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpaceRequest;
use App\Models\TrainingRequest;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class RequestPdfController extends Controller
{
    public function training(TrainingRequest $record): Response
    {
        $user = $this->currentUser();

        abort_unless($user && $user->canAccessTrainingRequest($record), Response::HTTP_FORBIDDEN);

        $pdf = Pdf::loadView('pdf.training-request', [
            'record' => $record,
            'logoPath' => $this->resolvePdfLogoPath(),
            'statusLabel' => $this->trainingStatusLabel($record->status),
            'classTypeLabel' => $record->class_type === 'EXCLUSIVA' ? 'Exclusiva' : 'Aberta',
            'scopeLabel' => $record->scope_label,
            'institutionLabel' => $record->isMunicipality() ? 'Município' : 'Órgão/Secretaria',
        ])->setPaper('a4');

        return $pdf->download("solicitacao-curso-{$record->protocol}.pdf");
    }

    public function space(SpaceRequest $record): Response
    {
        $user = $this->currentUser();

        abort_unless($user && $user->canAccessSpaceRequests(), Response::HTTP_FORBIDDEN);

        $pdf = Pdf::loadView('pdf.space-request', [
            'record' => $record,
            'logoPath' => $this->resolvePdfLogoPath(),
            'statusLabel' => $this->spaceStatusLabel($record->status),
            'timeSlotLabel' => $this->timeSlotLabel($record->time_slot),
        ])->setPaper('a4');

        return $pdf->download("cessao-espaco-{$record->id}.pdf");
    }

    private function trainingStatusLabel(string $status): string
    {
        return [
            'nao_iniciado' => 'Não iniciado',
            'em_andamento' => 'Em andamento',
            'realizado' => 'Realizado',
            'nao_realizado' => 'Não realizado',
        ][$status] ?? $status;
    }

    private function spaceStatusLabel(string $status): string
    {
        return [
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'recusado' => 'Recusado',
            'cancelado' => 'Cancelado',
        ][$status] ?? $status;
    }

    private function timeSlotLabel(string $timeSlot): string
    {
        return [
            'manha' => 'Manhã',
            'tarde' => 'Tarde',
            'manha_tarde' => 'Manhã e Tarde',
        ][$timeSlot] ?? $timeSlot;
    }

    private function resolvePdfLogoPath(): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $path = public_path('images/logo-egpce.png');

        return file_exists($path) ? $path : null;
    }

    private function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
