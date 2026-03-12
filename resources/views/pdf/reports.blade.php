<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Solicitações</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 11px; margin: 28px; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 14px; margin-bottom: 18px; }
        .header table { width: 100%; }
        .header .brand-cell { width: 220px; }
        .header img { width: 180px; height: auto; display: block; }
        .brand-fallback { font-size: 28px; font-weight: bold; color: #115e59; letter-spacing: 0.02em; }
        h1 { margin: 0; font-size: 21px; color: #115e59; }
        .subtitle { margin-top: 6px; color: #4b5563; }
        .summary { margin: 18px 0; }
        .summary td { width: 33.33%; padding: 10px; border: 1px solid #d1d5db; background: #f8fafc; }
        .metric-label { font-size: 10px; text-transform: uppercase; color: #64748b; }
        .metric-value { font-size: 24px; font-weight: bold; color: #0f172a; margin-top: 4px; }
        .section { margin-top: 18px; }
        .section h2 { font-size: 14px; color: #0f172a; margin-bottom: 10px; }
        .status-grid td { width: 50%; padding: 8px 10px; border: 1px solid #e5e7eb; }
        table.list { width: 100%; border-collapse: collapse; }
        table.list th, table.list td { border: 1px solid #e5e7eb; padding: 8px 10px; text-align: left; }
        table.list th { background: #f3f4f6; }
        .filters { margin-top: 10px; font-size: 10px; color: #475569; line-height: 1.6; }
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
                    <h1>Relatório de Solicitações</h1>
                    <div class="subtitle">Gerado em {{ now()->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>

        <div class="filters">
            Tipo do módulo: {{ $filters['type'] ?? 'ambos' }} |
            Tipo da solicitação (curso): {{ $trainingScopeLabels[$filters['training_scope'] ?? ''] ?? 'todos' }} |
            Órgão (Estado): {{ $filters['state_institution_name'] ?? 'todos' }} |
            Município: {{ $filters['municipality_name'] ?? 'todos' }} |
            Órgão (geral): {{ $filters['institution_name'] ?? 'todos' }} |
            Status: {{ $filters['status'] ?? 'todos' }} |
            Busca: {{ $filters['search'] ?? '-' }}
        </div>
    </div>

    <table class="summary" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="metric-label">Total Geral</div>
                <div class="metric-value">{{ $summary['training_total'] + $summary['space_total'] }}</div>
            </td>
            <td>
                <div class="metric-label">Curso</div>
                <div class="metric-value">{{ $summary['training_total'] }}</div>
            </td>
            <td>
                <div class="metric-label">Espaço</div>
                <div class="metric-value">{{ $summary['space_total'] }}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <h2>Status de Curso</h2>
        <table class="status-grid" cellspacing="0" cellpadding="0" width="100%">
            @foreach($trainingStatusLabels as $status => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td style="text-align: right;">{{ $trainingStatuses[$status] ?? 0 }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Status de Espaço</h2>
        <table class="status-grid" cellspacing="0" cellpadding="0" width="100%">
            @foreach($spaceStatusLabels as $status => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td style="text-align: right;">{{ $spaceStatuses[$status] ?? 0 }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Últimas Solicitações de Curso</h2>
        <table class="list">
            <thead>
                <tr>
                    <th>Protocolo</th>
                    <th>Tipo da solicitação</th>
                    <th>Órgão/Município</th>
                    <th>Tipo de formação</th>
                    <th>Status</th>
                    <th>Criado em</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trainingItems as $item)
                    <tr>
                        <td>{{ $item->protocol }}</td>
                        <td>{{ $item->scope_label }}</td>
                        <td>{{ $item->institution_name }}</td>
                        <td>{{ $item->training_type }}</td>
                        <td>{{ $trainingStatusLabels[$item->status] ?? $item->status }}</td>
                        <td>{{ optional($item->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">Nenhuma solicitação de curso encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Últimas Cessões de Espaço</h2>
        <table class="list">
            <thead>
                <tr>
                    <th>Órgão</th>
                    <th>Evento</th>
                    <th>Status</th>
                    <th>Criado em</th>
                </tr>
            </thead>
            <tbody>
                @forelse($spaceItems as $item)
                    <tr>
                        <td>{{ $item->institution_name }}</td>
                        <td>{{ $item->event_title }}</td>
                        <td>{{ $spaceStatusLabels[$item->status] ?? $item->status }}</td>
                        <td>{{ optional($item->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">Nenhuma cessão de espaço encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
