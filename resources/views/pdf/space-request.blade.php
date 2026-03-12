<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cessão de Espaço #{{ $record->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; margin: 32px; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 16px; margin-bottom: 24px; }
        .header table { width: 100%; }
        .header .brand-cell { width: 220px; }
        .header img { width: 180px; height: auto; display: block; }
        .brand-fallback { font-size: 28px; font-weight: bold; color: #115e59; letter-spacing: 0.02em; }
        h1 { margin: 0; font-size: 22px; color: #115e59; }
        .subtitle { margin-top: 6px; color: #4b5563; }
        .status { display: inline-block; padding: 6px 12px; border-radius: 999px; background: #ecfeff; color: #0f766e; font-weight: bold; }
        .section { margin-top: 20px; border: 1px solid #d1d5db; border-radius: 12px; overflow: hidden; }
        .section-title { background: #f3f4f6; padding: 10px 14px; font-weight: bold; color: #111827; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { width: 50%; vertical-align: top; padding: 10px 14px; border-top: 1px solid #e5e7eb; }
        .label { font-size: 10px; text-transform: uppercase; color: #6b7280; margin-bottom: 4px; }
        .value { font-size: 12px; color: #111827; }
        .full { width: 100%; }
        ul { padding-left: 18px; margin: 0; }
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
                    <h1>Cessão de Espaço</h1>
                    <div class="subtitle">Registro #{{ $record->id }}</div>
                    <div style="margin-top: 8px;"><span class="status">{{ $statusLabel }}</span></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Órgão e responsável</div>
        <table class="grid">
            <tr>
                <td><div class="label">Órgão/Secretaria</div><div class="value">{{ $record->institution_name }}</div></td>
                <td><div class="label">Responsável</div><div class="value">{{ $record->responsible_name }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Cargo</div><div class="value">{{ $record->responsible_role }}</div></td>
                <td><div class="label">E-mail</div><div class="value">{{ $record->responsible_email }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Telefone</div><div class="value">{{ $record->responsible_phone }}</div></td>
                <td><div class="label">Termo aceito em</div><div class="value">{{ optional($record->accepted_terms_at)->format('d/m/Y H:i') ?: 'Não registrado' }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Dados da formação</div>
        <table class="grid">
            <tr>
                <td colspan="2" class="full"><div class="label">Título da formação</div><div class="value">{{ $record->event_title }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Data de início</div><div class="value">{{ optional($record->start_date)->format('d/m/Y') }}</div></td>
                <td><div class="label">Data de fim</div><div class="value">{{ optional($record->end_date)->format('d/m/Y') }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Horário</div><div class="value">{{ $timeSlotLabel }}</div></td>
                <td><div class="label">Quantidade de participantes</div><div class="value">{{ $record->participants_quantity }}</div></td>
            </tr>
            <tr>
                <td colspan="2" class="full"><div class="label">Objetivos</div><div class="value">{{ $record->objective }}</div></td>
            </tr>
            <tr>
                <td colspan="2" class="full"><div class="label">Público participante</div><div class="value">{{ $record->target_audience }}</div></td>
            </tr>
            <tr>
                <td colspan="2" class="full"><div class="label">Observações gerais</div><div class="value">{{ $record->general_notes ?: 'Sem observações registradas.' }}</div></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Espaços solicitados</div>
        <table class="grid">
            <tr>
                <td colspan="2" class="full">
                    @if(is_array($record->selected_spaces_snapshot) && count($record->selected_spaces_snapshot))
                        <ul>
                            @foreach($record->selected_spaces_snapshot as $space)
                                <li>
                                    {{ $space['label'] ?? ($space['key'] ?? 'Espaço') }}
                                    @if(!empty($space['capacity']))
                                        - capacidade {{ $space['capacity'] }} pessoas
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="value">Nenhum espaço snapshot registrado.</div>
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2" class="full"><div class="label">Arquivo do termo</div><div class="value">{{ $record->responsibility_term_path ?: 'Sem arquivo' }}</div></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Gerado em {{ now()->format('d/m/Y H:i') }}. Registro criado em {{ optional($record->created_at)->format('d/m/Y H:i') }} e atualizado em {{ optional($record->updated_at)->format('d/m/Y H:i') }}.
    </div>
</body>
</html>
