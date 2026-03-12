<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Solicitação de Curso {{ $record->protocol }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; margin: 32px; }
        .header { border-bottom: 2px solid #059669; padding-bottom: 16px; margin-bottom: 24px; }
        .header table { width: 100%; }
        .header .brand-cell { width: 220px; }
        .header img { width: 180px; height: auto; display: block; }
        .brand-fallback { font-size: 28px; font-weight: bold; color: #065f46; letter-spacing: 0.02em; }
        h1 { margin: 0; font-size: 22px; color: #065f46; }
        .subtitle { margin-top: 6px; color: #4b5563; }
        .status { display: inline-block; padding: 6px 12px; border-radius: 999px; background: #ecfdf5; color: #047857; font-weight: bold; }
        .section { margin-top: 20px; border: 1px solid #d1d5db; border-radius: 12px; overflow: hidden; }
        .section-title { background: #f3f4f6; padding: 10px 14px; font-weight: bold; color: #111827; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { width: 50%; vertical-align: top; padding: 10px 14px; border-top: 1px solid #e5e7eb; }
        .label { font-size: 10px; text-transform: uppercase; color: #6b7280; margin-bottom: 4px; }
        .value { font-size: 12px; color: #111827; }
        .full { width: 100%; }
        .footer { margin-top: 24px; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td class="brand-cell">
                    @if($logoPath && file_exists($logoPath))
                        <img src="{{ $logoPath }}" alt="EGPCE">
                    @else
                        <div class="brand-fallback">EGPCE</div>
                    @endif
                </td>
                <td style="text-align: right;">
                    <h1>Solicitação de Curso</h1>
                    <div class="subtitle">Protocolo {{ $record->protocol }}</div>
                    <div style="margin-top: 8px;"><span class="status">{{ $statusLabel }}</span></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Dados gerais</div>
        <table class="grid">
            <tr>
                <td><div class="label">Tipo da solicitação</div><div class="value">{{ $scopeLabel }}</div></td>
                <td><div class="label">Tipo de evento</div><div class="value">{{ $record->event_type }}</div></td>
            </tr>
            <tr>
                <td><div class="label">{{ $institutionLabel }}</div><div class="value">{{ $record->institution_name }}</div></td>
                <td><div class="label">Tipo de formação</div><div class="value">{{ $record->training_type }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Tipo de turma</div><div class="value">{{ $classTypeLabel }}</div></td>
                <td><div class="label">Participantes</div><div class="value">{{ $record->participants_count }}</div></td>
            </tr>
            <tr>
                <td colspan="2" class="full"><div class="label">Participação de lideranças</div><div class="value">{{ $record->leaders_participation ? 'Sim' : 'Não' }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Responsáveis</div>
        <table class="grid">
            <tr>
                <td><div class="label">Titular da instituição</div><div class="value">{{ $record->holder_name }}</div></td>
                <td><div class="label">Cargo do titular</div><div class="value">{{ $record->holder_role }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Responsável pela solicitação</div><div class="value">{{ $record->requester_name }}</div></td>
                <td><div class="label">Cargo do responsável</div><div class="value">{{ $record->requester_role }}</div></td>
            </tr>
            <tr>
                <td><div class="label">E-mail</div><div class="value">{{ $record->requester_email }}</div></td>
                <td><div class="label">Telefone</div><div class="value">{{ $record->requester_phone }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Conteúdo da solicitação</div>
        <table class="grid">
            <tr>
                <td colspan="2" class="full"><div class="label">Público-alvo</div><div class="value">{{ $record->target_audience }}</div></td>
            </tr>
            <tr>
                <td colspan="2" class="full"><div class="label">Objetivos</div><div class="value">{{ $record->objectives }}</div></td>
            </tr>
            <tr>
                <td colspan="2" class="full"><div class="label">Expectativa de conteúdo</div><div class="value">{{ $record->content_expectation }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Ofício</div><div class="value">{{ $record->request_letter_path }}</div></td>
                <td><div class="label">Termos aceitos</div><div class="value">{{ $record->terms_accepted ? 'Sim' : 'Não' }}</div></td>
            </tr>
            <tr>
                <td colspan="2" class="full"><div class="label">Observações da administração</div><div class="value">{{ $record->admin_notes ?: 'Sem observações registradas.' }}</div></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Gerado em {{ now()->format('d/m/Y H:i') }}. Registro criado em {{ optional($record->created_at)->format('d/m/Y H:i') }} e atualizado em {{ optional($record->updated_at)->format('d/m/Y H:i') }}.
    </div>
</body>
</html>
