<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 9.5pt;
    color: #1a1a2e;
    background: #fff;
    padding: 28px 32px;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 2.5px solid #0E3B2E;
    padding-bottom: 14px;
    margin-bottom: 20px;
}
.header-brand {
    font-size: 15pt;
    font-weight: 700;
    color: #0E3B2E;
    letter-spacing: .02em;
}
.header-brand span { color: #C6A15B; }
.header-meta {
    text-align: right;
    font-size: 8pt;
    color: #555;
    line-height: 1.6;
}
.doc-title {
    font-size: 13pt;
    font-weight: 700;
    color: #0E3B2E;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 18px;
}
.summary-grid {
    display: table;
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 22px;
    background: #f8f9fc;
    border: 1px solid #dde2ef;
    border-radius: 4px;
}
.summary-row { display: table-row; }
.summary-cell {
    display: table-cell;
    padding: 7px 14px;
    font-size: 8.5pt;
    border-bottom: 1px solid #dde2ef;
    width: 25%;
}
.summary-cell.label {
    color: #666;
    font-weight: 600;
}
.summary-cell.value {
    color: #0E3B2E;
    font-weight: 700;
}
.summary-cell.value-accent { color: #C6A15B; font-weight: 700; }

table.schedule {
    width: 100%;
    border-collapse: collapse;
    font-size: 8.5pt;
}
table.schedule thead tr {
    background: #0E3B2E;
    color: #fff;
}
table.schedule thead th {
    padding: 7px 10px;
    text-align: right;
    font-weight: 600;
    letter-spacing: .03em;
    white-space: nowrap;
}
table.schedule thead th:first-child { text-align: center; }
table.schedule tbody tr:nth-child(even) { background: #f4f6fb; }
table.schedule tbody tr td {
    padding: 5px 10px;
    text-align: right;
    border-bottom: 1px solid #e8ecf5;
    color: #222;
}
table.schedule tbody tr td:first-child {
    text-align: center;
    color: #666;
    font-weight: 600;
}
table.schedule tbody tr td.interest { color: #c0392b; }
table.schedule tbody tr td.balance  { color: #0E3B2E; font-weight: 600; }
table.schedule tfoot tr {
    background: #0E3B2E;
    color: #fff;
}
table.schedule tfoot td {
    padding: 7px 10px;
    text-align: right;
    font-weight: 700;
    border: none;
}
table.schedule tfoot td:first-child { text-align: center; }
.footer {
    margin-top: 24px;
    padding-top: 10px;
    border-top: 1px solid #dde2ef;
    font-size: 7.5pt;
    color: #888;
    text-align: center;
}
</style>
</head>
<body>

<div class="header">
    <div>
        @if(!empty($logoBase64))
        <img src="{{ $logoBase64 }}" style="height:42px;max-width:190px;object-fit:contain;display:block">
    @else
        @php
            $brandWords = preg_split('/\s+/', trim(site_name())) ?: [];
            $brandFirst = array_shift($brandWords);
            $brandRest  = implode(' ', $brandWords);
        @endphp
        <div class="header-brand">{{ $brandFirst }}@if($brandRest) <span>{{ $brandRest }}</span>@endif</div>
    @endif
        <div style="font-size:7.5pt;color:#888;margin-top:3px">{{ $texts['header_sub'] }}</div>
    </div>
    <div class="header-meta">
        <div><strong>{{ $texts['ref'] }} :</strong> {{ $loan->reference }}</div>
        <div><strong>{{ $texts['date'] }} :</strong> {{ now()->format('d/m/Y') }}</div>
        <div><strong>{{ $loan->name }}</strong></div>
    </div>
</div>

<div class="doc-title">{{ $texts['title'] }}</div>

{{-- Résumé du financement --}}
<div class="summary-grid">
    <div class="summary-row">
        <div class="summary-cell label">{{ $texts['amount'] }}</div>
        <div class="summary-cell value">{{ number_format($loan->amount, 2, ',', ' ') }} {{ $loan->currency }}</div>
        <div class="summary-cell label">{{ $texts['duration'] }}</div>
        <div class="summary-cell value">{{ $loan->darly }} {{ $texts['months'] }}</div>
    </div>
    <div class="summary-row">
        <div class="summary-cell label">{{ $texts['monthly'] }}</div>
        <div class="summary-cell value-accent">{{ number_format($loan->monthly_payment, 2, ',', ' ') }} {{ $loan->currency }}</div>
        <div class="summary-cell label">{{ $texts['rate'] }}</div>
        <div class="summary-cell value">{{ $loan->interest_rate }} %</div>
    </div>
    <div class="summary-row">
        <div class="summary-cell label">{{ $texts['total_interest'] }}</div>
        <div class="summary-cell value">{{ number_format($loan->total_cost, 2, ',', ' ') }} {{ $loan->currency }}</div>
        <div class="summary-cell label">{{ $texts['total_repaid'] }}</div>
        <div class="summary-cell value">{{ number_format($loan->total_with_interest, 2, ',', ' ') }} {{ $loan->currency }}</div>
    </div>
</div>

{{-- Tableau d'amortissement --}}
<table class="schedule">
    <thead>
        <tr>
            <th style="text-align:center">{{ $texts['col_month'] }}</th>
            <th>{{ $texts['col_payment'] }}</th>
            <th>{{ $texts['col_principal'] }}</th>
            <th>{{ $texts['col_interest'] }}</th>
            <th>{{ $texts['col_balance'] }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($schedule as $row)
        <tr>
            <td>{{ $row['month'] }}</td>
            <td>{{ number_format($row['payment'], 2, ',', ' ') }}</td>
            <td>{{ number_format($row['principal'], 2, ',', ' ') }}</td>
            <td class="interest">{{ number_format($row['interest'], 2, ',', ' ') }}</td>
            <td class="balance">{{ number_format($row['balance'], 2, ',', ' ') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>{{ $texts['total'] }}</td>
            <td>{{ number_format(collect($schedule)->sum('payment'), 2, ',', ' ') }}</td>
            <td>{{ number_format(collect($schedule)->sum('principal'), 2, ',', ' ') }}</td>
            <td>{{ number_format(collect($schedule)->sum('interest'), 2, ',', ' ') }}</td>
            <td>—</td>
        </tr>
    </tfoot>
</table>

{{-- La concatenation PHP etait restee telle quelle dans le HTML : le pied de
     page affichait litteralement « — ' . site_name() . ' INVESTI © ». --}}
<div class="footer">{{ $texts['footer'] }} — {{ site_name() }} © {{ now()->format('Y') }}</div>

</body>
</html>
