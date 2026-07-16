<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #09090B; font-size: 11px; margin: 0; padding: 32px; }
        .head { display: flex; justify-content: space-between; border-bottom: 1px solid #09090B; padding-bottom: 12px; margin-bottom: 16px; }
        .eyebrow { font-size: 8px; letter-spacing: 1px; text-transform: uppercase; color: #9F9F9F; }
        h1 { font-size: 16px; font-weight: 500; margin: 2px 0 0; }
        .meta { width: 100%; font-size: 10px; color: #585858; margin-bottom: 16px; }
        .meta td { padding: 2px 0; }
        .meta strong { color: #09090B; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.data th { text-align: left; border-bottom: 1px solid #09090B; padding: 6px 0; font-weight: bold; }
        table.data td { border-bottom: 1px solid #E4E4E7; padding: 8px 0; }
        table.data .num { text-align: right; }
        .total td { border-top: 2px solid #09090B; border-bottom: 0; font-weight: bold; padding-top: 8px; }
        .footer { margin-top: 24px; border-top: 1px solid #E4E4E7; padding-top: 8px; font-size: 8px; color: #9F9F9F; text-align: center; }
        .empty { padding: 24px 0; color: #9F9F9F; text-align: center; }
    </style>
</head>
<body>
    <div class="head">
        <div>
            <div class="eyebrow">Récapitulatif</div>
            <h1>Temps de travail</h1>
        </div>
    </div>

    <table class="meta">
        <tr>
            <td><strong>Entreprise</strong><br>{{ $company->name }}</td>
            <td><strong>Période</strong><br>{{ $from->format('d/m/Y') }} → {{ $to->format('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Employé</th>
                <th class="num">Heures travaillées</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['employee']->first_name }} {{ $row['employee']->last_name }}</td>
                    <td class="num">{{ \App\Services\WorkHoursReport::formatMinutes($row['minutes']) }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="empty">Aucun pointage sur la période.</td></tr>
            @endforelse
            @if (! empty($rows))
                <tr class="total">
                    <td>Total</td>
                    <td class="num">{{ \App\Services\WorkHoursReport::formatMinutes($totalMinutes) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Généré le {{ $generatedAt->format('d/m/Y à H:i') }} · Huma·log
    </div>
</body>
</html>
