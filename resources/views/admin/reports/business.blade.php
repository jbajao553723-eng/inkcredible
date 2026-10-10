<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inkcredible business performance report</title>
    <style>
        @page { margin: 25px 28px 44px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #1d2939; font-family: DejaVu Sans, sans-serif; font-size: 8px; line-height: 1.38; }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        .masthead { margin-bottom: 11px; color: #fff; background: #101828; border-left: 7px solid #7c3aed; border-radius: 8px; }
        .masthead td { padding: 15px 17px; border: 0; }
        .brand { color: #a5b4fc; font-size: 7px; font-weight: bold; letter-spacing: 1.2px; text-transform: uppercase; }
        h1 { margin: 4px 0 4px; font-size: 21px; line-height: 1.15; }
        .subtitle { color: #d0d5dd; font-size: 8px; }
        .report-meta { width: 34%; color: #d0d5dd; line-height: 1.65; text-align: right; }
        .confidential { display: inline-block; margin-bottom: 4px; padding: 3px 7px; color: #fff; background: #4f46e5; border-radius: 8px; font-size: 6px; font-weight: bold; letter-spacing: .5px; }
        .scope { padding: 8px 10px; color: #475467; background: #f8fafc; border: 1px solid #e4e7ec; border-radius: 4px; }
        .download-attribution { margin: 0 0 10px; padding: 7px 10px; color: #344054; background: #f8fafc; border-left: 3px solid #b91c1c; overflow-wrap: break-word; }
        .section { margin-top: 12px; }
        .section.break { page-break-before: always; margin-top: 0; }
        .section-heading { margin: 0 0 6px; padding-bottom: 5px; color: #101828; border-bottom: 1px solid #d0d5dd; font-size: 10px; }
        .section-note { margin: -2px 0 7px; color: #667085; font-size: 7px; }
        .metrics { table-layout: fixed; border-spacing: 5px; border-collapse: separate; margin: -5px; width: calc(100% + 10px); }
        .metrics td { width: 25%; padding: 9px; background: #f8fafc; border: 1px solid #e4e7ec; border-radius: 5px; }
        .label { color: #667085; font-size: 6.5px; font-weight: bold; letter-spacing: .35px; text-transform: uppercase; }
        .metric-value { margin-top: 3px; color: #312e81; font-size: 12.5px; font-weight: bold; }
        .metric-note { margin-top: 2px; color: #667085; font-size: 6.5px; }
        .data { table-layout: fixed; }
        .data thead { display: table-header-group; }
        .data th { padding: 5px; color: #475467; background: #f2f4f7; border: 1px solid #d0d5dd; font-size: 6.2px; letter-spacing: .2px; text-align: left; text-transform: uppercase; }
        .data td { padding: 5px; border: 1px solid #e4e7ec; overflow-wrap: break-word; }
        .data tbody tr:nth-child(even) td { background: #fcfcfd; }
        .data .amount, .data .rate { text-align: right; white-space: nowrap; }
        .data .count { text-align: center; }
        .status { display: inline-block; padding: 2px 5px; border-radius: 7px; font-size: 6px; font-weight: bold; }
        .good { color: #05603a; background: #dcfae6; }
        .warn { color: #93370d; background: #fef0c7; }
        .risk { color: #912018; background: #fee4e2; }
        .neutral { color: #344054; background: #eaecf0; }
        .two-column { table-layout: fixed; }
        .two-column > tbody > tr > td { width: 50%; border: 0; }
        .two-column > tbody > tr > td:first-child { padding-right: 7px; }
        .two-column > tbody > tr > td:last-child { padding-left: 7px; }
        .control-box { padding: 8px; background: #fffaeb; border: 1px solid #fedf89; }
        .control-row { padding: 4px 0; border-bottom: 1px solid #fde7b2; }
        .control-row:last-child { border-bottom: 0; }
        .control-count { float: right; font-weight: bold; }
        .methodology { margin-top: 10px; padding: 8px 10px; color: #475467; background: #f5f3ff; border-left: 3px solid #6366f1; font-size: 7px; }
        .methodology ul { margin: 4px 0 0; padding-left: 14px; }
        .methodology li { margin: 2px 0; }
        .profit-band { width: 100%; margin-top: 11px; color: #fff; background: #312e81; border-radius: 6px; page-break-inside: avoid; }
        .profit-band td { padding: 12px 13px; border: 0; vertical-align: middle; }
        .profit-main { width: 40%; border-right: 1px solid #4f46e5 !important; }
        .profit-main .label { color: #c7d2fe; }
        .profit-value { margin-top: 3px; font-size: 20px; font-weight: bold; line-height: 1.1; }
        .profit-note { margin-top: 4px; color: #c7d2fe; font-size: 6.5px; }
        .profit-stat { width: 20%; }
        .profit-stat span { display: block; color: #c7d2fe; font-size: 6px; font-weight: bold; letter-spacing: .3px; text-transform: uppercase; }
        .profit-stat strong { display: block; margin-top: 4px; font-size: 11px; }
        .briefing { table-layout: fixed; border-spacing: 6px; border-collapse: separate; width: calc(100% + 12px); margin: 5px -6px -6px; }
        .briefing td { width: 33.33%; padding: 8px 9px; background: #fff; border: 1px solid #e4e7ec; border-top: 3px solid #6366f1; border-radius: 4px; }
        .briefing td.good-card { border-top-color: #12b76a; }
        .briefing td.warn-card { border-top-color: #f79009; }
        .briefing td.risk-card { border-top-color: #f04438; }
        .briefing-title { color: #344054; font-size: 7px; font-weight: bold; text-transform: uppercase; }
        .briefing-value { margin-top: 4px; color: #101828; font-size: 10px; font-weight: bold; }
        .briefing-copy { margin-top: 3px; color: #667085; font-size: 6.4px; line-height: 1.45; }
        .chart-card { padding: 9px 10px 8px; background: #fbfcff; border: 1px solid #d9def0; border-radius: 5px; page-break-inside: avoid; }
        .chart-title { color: #344054; font-size: 8px; font-weight: bold; }
        .chart-subtitle { margin: 2px 0 7px; color: #667085; font-size: 6.5px; }
        .chart { table-layout: fixed; }
        .chart td { padding: 2px 3px; border: 0; vertical-align: middle; }
        .chart .month { width: 10%; color: #344054; font-size: 6.7px; font-weight: bold; }
        .chart .series { width: 9%; color: #667085; font-size: 6px; }
        .chart .plot { width: 61%; }
        .chart .chart-amount { width: 20%; color: #344054; font-size: 6.5px; font-weight: bold; text-align: right; white-space: nowrap; }
        .track { width: 100%; height: 7px; overflow: hidden; background: #eaecf0; border-radius: 4px; }
        .bar { height: 7px; border-radius: 4px; }
        .bar-principal { background: #4f46e5; }
        .bar-collections { background: #12b76a; }
        .legend { margin-top: 6px; color: #667085; font-size: 6.5px; text-align: right; }
        .legend-key { display: inline-block; width: 7px; height: 7px; margin: 0 3px 0 9px; border-radius: 2px; vertical-align: -1px; }
        .insight { margin-top: 7px; padding: 6px 8px; color: #344054; background: #eef2ff; border-left: 3px solid #6366f1; font-size: 6.8px; }
        .empty { padding: 10px; color: #667085; background: #f8fafc; border: 1px solid #e4e7ec; text-align: center; }
        .keep-together { page-break-inside: avoid; }
        .page-footer { position: fixed; right: 0; bottom: -28px; left: 0; padding-top: 6px; color: #667085; border-top: 1px solid #d0d5dd; font-size: 6.5px; }
        .page-footer td { width: 33.33%; }
        .page-footer .center { text-align: center; }
        .page-footer .right { text-align: right; }
        .page-number:before { content: counter(page); }
        @include('partials.pdf-styles')
        .metrics,.briefing { width:100%; margin:0; border-spacing:5px; }
        .metric-value { color:#172033; font-size:12px; }
        .metrics td { padding:10px; }
        .section-heading { color:#172033; font-size:11px; }
        .profit-band { background:#172033; }
        .profit-main { border-right-color:#475467!important; }
        .profit-main .label,.profit-note,.profit-stat span { color:#d0d5dd; }
        .data th { font-size:7px; padding:6px; background:#f0f3f7; }
        .data td { padding:7px 6px; }
        .data .amount { white-space:normal; }
        .methodology { background:#f8fafc; border-color:#b91c1c; }
        .document-header { margin-bottom:12px; }
        .document-title { font-size:23px; }
        .document-subtitle { margin-bottom:12px; }
        .table-caption { margin:8px 0 5px; color:#667085; font-size:7px; font-weight:bold; text-transform:uppercase; }
        .two-column { page-break-before:always; }
    </style>
</head>
<body>
@php
    $controlIssues = collect($controls)->sum('count');
    $monthlyMaximum = max(1, (float) collect($monthlyTrend)->max(
        fn ($month) => max($month['principal_released'], $month['collections'])
    ));
    $strongestCollectionMonth = collect($monthlyTrend)->sortByDesc('collections')->first();
    $projectedGrossProfit = (float) $summary['contract_interest'] + (float) $summary['penalties'];
    $projectedRevenue = (float) $summary['scheduled_payable'] + (float) $summary['penalties'];
    $projectedMargin = $projectedRevenue > 0 ? ($projectedGrossProfit / $projectedRevenue) * 100 : 0;
    $collectionGap = max(0, $projectedRevenue - (float) $summary['collections']);
    $riskLabel = $summary['overdue_share'] >= 25 ? 'High attention' : ($summary['overdue_share'] > 0 ? 'Watch closely' : 'No overdue exposure');
@endphp
@include('partials.pdf-header', [
    'documentReference' => 'BR-'.$preparedAt->format('Ymd-Hi'),
    'documentDate' => $preparedAt->format('M d, Y - h:i A'),
    'documentClassification' => 'Confidential - management use',
    'documentCategory' => 'Management reporting',
    'documentTitle' => 'Business performance report',
    'documentSubtitle' => 'Portfolio, collections, credit risk, and operational controls. All recorded activity; amounts in PHP.',
])

<div class="download-attribution"><strong>Downloaded by:</strong> {{ $downloadedByName ?? 'Administrator' }}</div>

<div class="scope"><strong>Reporting basis:</strong> This report uses approved payment records as the collection ledger, approved and paid loans as the originated portfolio, and installment schedules for penalty and overdue exposure. Pending and rejected transactions are excluded from recognized collections.</div>

<div class="section">
    <h2 class="section-heading">1. Executive summary</h2>
    <table class="metrics">
        <tr>
            <td><div class="label">Registered clients</div><div class="metric-value">{{ number_format($summary['clients']) }}</div><div class="metric-note">{{ number_format($summary['verified_clients']) }} verified</div></td>
            <td><div class="label">Loan applications</div><div class="metric-value">{{ number_format($summary['applications']) }}</div><div class="metric-note">{{ number_format($summary['originated_loans']) }} originated</div></td>
            <td><div class="label">Principal released</div><div class="metric-value">PHP {{ number_format($summary['principal_released'], 2) }}</div><div class="metric-note">Approved and completed loans</div></td>
            <td><div class="label">Scheduled receivables</div><div class="metric-value">PHP {{ number_format($summary['scheduled_payable'], 2) }}</div><div class="metric-note">Includes PHP {{ number_format($summary['contract_interest'], 2) }} contractual interest</div></td>
        </tr>
        <tr>
            <td><div class="label">Approved collections</div><div class="metric-value">PHP {{ number_format($summary['collections'], 2) }}</div><div class="metric-note">{{ number_format($summary['collection_rate'], 2) }}% of scheduled receivables</div></td>
            <td><div class="label">Outstanding portfolio</div><div class="metric-value">PHP {{ number_format($summary['outstanding'], 2) }}</div><div class="metric-note">Includes PHP {{ number_format($summary['penalties'], 2) }} penalties</div></td>
            <td><div class="label">Overdue exposure</div><div class="metric-value">PHP {{ number_format($summary['overdue_amount'], 2) }}</div><div class="metric-note">{{ number_format($summary['overdue_share'], 2) }}% of outstanding portfolio</div></td>
            <td><div class="label">Loan completion rate</div><div class="metric-value">{{ number_format($summary['completion_rate'], 2) }}%</div><div class="metric-note">{{ number_format($summary['completed_loans']) }} completed of {{ number_format($summary['originated_loans']) }} originated</div></td>
        </tr>
    </table>
</div>

<table class="profit-band"><tr>
    <td class="profit-main"><div class="label">Projected gross profit</div><div class="profit-value">PHP {{ number_format($projectedGrossProfit, 2) }}</div><div class="profit-note">Contract interest plus recorded penalties, before operating costs and defaults</div></td>
    <td class="profit-stat"><span>Projected margin</span><strong>{{ number_format($projectedMargin, 1) }}%</strong></td>
    <td class="profit-stat"><span>Contract interest</span><strong>PHP {{ number_format($summary['contract_interest'], 2) }}</strong></td>
    <td class="profit-stat"><span>Collection gap</span><strong>PHP {{ number_format($collectionGap, 2) }}</strong></td>
</tr></table>

<div class="section">
    <h2 class="section-heading">2. Management briefing</h2>
    <table class="briefing"><tr>
        <td class="{{ $summary['collection_rate'] >= 75 ? 'good-card' : 'warn-card' }}"><div class="briefing-title">Collection performance</div><div class="briefing-value">{{ number_format($summary['collection_rate'], 1) }}% collected</div><div class="briefing-copy">PHP {{ number_format($summary['collections'], 2) }} in approved collections against PHP {{ number_format($projectedRevenue, 2) }} in scheduled receivables and penalties.</div></td>
        <td class="{{ $summary['overdue_share'] > 0 ? 'risk-card' : 'good-card' }}"><div class="briefing-title">Credit risk</div><div class="briefing-value">{{ $riskLabel }}</div><div class="briefing-copy">{{ number_format($summary['overdue_loans']) }} overdue loan(s) represent {{ number_format($summary['overdue_share'], 1) }}% of the outstanding portfolio.</div></td>
        <td class="{{ $controlIssues > 0 ? 'warn-card' : 'good-card' }}"><div class="briefing-title">Operational readiness</div><div class="briefing-value">{{ $controlIssues > 0 ? number_format($controlIssues).' exception(s)' : 'Controls clear' }}</div><div class="briefing-copy">{{ $controlIssues > 0 ? 'Resolve listed data exceptions before using this report for external decision-making.' : 'No listed data-quality exceptions were detected in this reporting snapshot.' }}</div></td>
    </tr></table>
</div>

<table class="two-column"><tr><td>
    <div class="section">
        <h2 class="section-heading">3. Portfolio status</h2>
        <table class="data"><thead><tr><th>Status</th><th class="count">Loans</th><th class="amount">Principal</th></tr></thead><tbody>
        @foreach($statuses as $row)
            <tr><td>{{ $row['label'] }}</td><td class="count">{{ number_format($row['count']) }}</td><td class="amount">PHP {{ number_format($row['principal'], 2) }}</td></tr>
        @endforeach
        </tbody></table>
    </div>
</td><td>
    <div class="section">
        <h2 class="section-heading">4. Operational controls</h2>
        <div class="control-box">
            @foreach($controls as $control)
                <div class="control-row"><span class="control-count {{ $control['count'] ? 'risk' : 'good' }} status">{{ number_format($control['count']) }}</span>{{ $control['label'] }}</div>
            @endforeach
        </div>
        <div class="section-note" style="margin-top:5px">{{ $controlIssues ? $controlIssues.' exception(s) require review.' : 'No listed control exceptions were detected.' }}</div>
    </div>
</td></tr></table>

<div class="section">
    <h2 class="section-heading">5. Product performance</h2>
    <div class="section-note">Collection rate equals approved collections divided by scheduled receivables for originated loans.</div>
    @if(empty($products))
        <div class="empty">No loan product activity is available.</div>
    @else
        <div class="table-caption">Product activity</div>
        <table class="data"><thead><tr><th style="width:28%">Product</th><th class="count">Applications</th><th class="count">Originated</th><th class="count">Active</th><th class="count">Completed</th><th class="count">Overdue loans</th></tr></thead><tbody>
        @foreach($products as $product)
            <tr>
                <td><strong>{{ $product['name'] }}</strong></td><td class="count">{{ $product['applications'] }}</td><td class="count">{{ $product['originated'] }}</td><td class="count">{{ $product['active'] }}</td><td class="count">{{ $product['completed'] }}</td>
                <td class="count">{{ $product['overdue_loans'] }}</td>
            </tr>
        @endforeach
        </tbody></table>
        <div class="table-caption">Product finances (PHP)</div>
        <table class="data"><thead><tr><th style="width:28%">Product</th><th class="amount">Principal</th><th class="amount">Scheduled</th><th class="amount">Collected</th><th class="amount">Outstanding</th><th class="rate">Collection rate</th></tr></thead><tbody>
        @foreach($products as $product)
            <tr><td><strong>{{ $product['name'] }}</strong></td><td class="amount">{{ number_format($product['principal'], 2) }}</td><td class="amount">{{ number_format($product['scheduled'], 2) }}</td><td class="amount">{{ number_format($product['collected'], 2) }}</td><td class="amount">{{ number_format($product['outstanding'], 2) }}</td><td class="rate">{{ number_format($product['collection_rate'], 2) }}%</td></tr>
        @endforeach
        </tbody></table>
    @endif
</div>

<div class="section break">
    <h2 class="section-heading">6. Monthly financial activity</h2>
    <div class="section-note">Six-month comparison of principal released and approved cash collections. Bar lengths share one PHP scale.</div>
    <div class="chart-card">
        <div class="chart-title">Principal released versus approved collections</div>
        <div class="chart-subtitle">Monthly values in Philippine pesos; zero activity is retained to keep the reporting period complete.</div>
        <table class="chart"><tbody>
        @foreach($monthlyTrend as $month)
            @php
                $principalWidth = ($month['principal_released'] / $monthlyMaximum) * 100;
                $collectionWidth = ($month['collections'] / $monthlyMaximum) * 100;
            @endphp
            <tr>
                <td class="month" rowspan="2">{{ $month['month'] }}</td>
                <td class="series">Released</td>
                <td class="plot"><div class="track"><div class="bar bar-principal" style="width:{{ number_format($principalWidth, 2, '.', '') }}%"></div></div></td>
                <td class="chart-amount">PHP {{ number_format($month['principal_released'], 2) }}</td>
            </tr>
            <tr>
                <td class="series">Collected</td>
                <td class="plot"><div class="track"><div class="bar bar-collections" style="width:{{ number_format($collectionWidth, 2, '.', '') }}%"></div></div></td>
                <td class="chart-amount">PHP {{ number_format($month['collections'], 2) }}</td>
            </tr>
        @endforeach
        </tbody></table>
        <div class="legend"><span class="legend-key bar-principal"></span>Principal released <span class="legend-key bar-collections"></span>Approved collections</div>
        <div class="insight"><strong>Management reading:</strong> {{ ($strongestCollectionMonth['collections'] ?? 0) > 0 ? $strongestCollectionMonth['month'].' recorded the highest approved collections at PHP '.number_format($strongestCollectionMonth['collections'], 2).'.' : 'No approved collections were recorded during this six-month reporting window.' }}</div>
    </div>
</div>

<div class="keep-together">
<div class="section">
    <h2 class="section-heading">7. Collection channels and payment pipeline</h2>
    <div class="section-note">Only approved payments are recognized as collections. Pending payments remain in the pipeline until provider confirmation or cash review.</div>
    @if(empty($paymentMethods))
        <div class="empty">No payment activity is available.</div>
    @else
        <table class="data"><thead><tr><th>Payment channel</th><th class="count">Approved transactions</th><th class="amount">Approved collections</th><th class="count">Pending transactions</th><th class="amount">Pending amount</th><th class="count">Rejected transactions</th></tr></thead><tbody>
        @foreach($paymentMethods as $method)
            <tr><td><strong>{{ $method['label'] }}</strong></td><td class="count">{{ $method['approved_count'] }}</td><td class="amount">PHP {{ number_format($method['approved_amount'], 2) }}</td><td class="count">{{ $method['pending_count'] }}</td><td class="amount">PHP {{ number_format($method['pending_amount'], 2) }}</td><td class="count">{{ $method['rejected_count'] }}</td></tr>
        @endforeach
        </tbody></table>
    @endif
</div>

<div class="methodology">
    <strong>Metric definitions and review notes</strong>
    <ul>
        <li><strong>Principal released:</strong> original principal on loans whose current status is approved or paid.</li>
        <li><strong>Scheduled receivables:</strong> total payable on originated loans before future late penalties.</li>
        <li><strong>Approved collections:</strong> payment records with approved status; pending and rejected payments are excluded.</li>
        <li><strong>Outstanding portfolio:</strong> scheduled receivables plus recorded penalties less approved collections, floored at zero per loan.</li>
        <li><strong>Overdue exposure:</strong> unpaid scheduled amount plus recorded penalty for installments marked overdue or past due as of the report timestamp.</li>
        <li>This is an operational management report, not an audited financial statement. Control exceptions should be resolved before external use.</li>
    </ul>
</div>
</div>

@include('partials.pdf-footer', ['documentFooter' => 'Business performance report | '.$preparedAt->format('Y-m-d')])
</body>
</html>
